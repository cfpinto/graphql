# graphql

A simple yet powerful GraphQL query builder
[![CircleCI](https://circleci.com/gh/cfpinto/graphql/tree/master.svg?style=svg)](https://circleci.com/gh/cfpinto/graphql/tree/master)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/fce20e3c3a194a77aca585e68fd48fc1)](https://www.codacy.com/gh/cfpinto/graphql/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=cfpinto/graphql&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/fce20e3c3a194a77aca585e68fd48fc1)](https://www.codacy.com/gh/cfpinto/graphql/dashboard?utm_source=github.com&utm_medium=referral&utm_content=cfpinto/graphql&utm_campaign=Badge_Coverage)

## Change log

A lot of rewriting was done on this version particularly at code organization and entity management.
Whit this level of rewriting keeping backwards compatibility is tricky but did my best to do so.

There are a few deprecation notices that will be dropped next version:

- Root level classes will be dropped in favour of context namespace (GraphQL\Mutation => GraphQL\Actions\Mutation)
- Parsers and Entities will be decoupled, you will be forced to inject Parsers in the Entities constructor
- The `on()` method is used as syntax sugar, might be deprecated in favour of passing InlineFragment instances in the
  use method

## How it works

Writing GraphQL queries can be a painful process. With GraphpQL query builder write PHP and get GraphQL

### Examples

When finding a Hero to be his sidekick, one finds himself overwhelmed with the number of Heroes out there.
There are all sort of Heroes so lets list them all

#### Fields

```php
$hero = new \GraphQL\Actions\Query('hero');
echo $hero->use('name')
    ->friends
        ->use('name')
    ->root()
        ->query();
``` 

will generate

```text
{
    hero {
        name
        friends {
            name
        }
    }
}
```

#### Arguments

A Hero will have many friends which can make it hard to walk through, it would be great limit the Hero's friends to 2

```php
$hero = new \GraphQL\Actions\Query('hero');
echo $hero->use('name')
    ->friends(['first'=>2])
        ->use('name')
    ->root()
        ->query();
```

will generate

```text
{
    hero {
        name
        friends(first: 2) {
            name
        }
    }
}
```

#### Going back the tree

Sometimes you might need to know more about this Hero, like when you want to know a hero friends and costumes. We then
need to go back in the Hero tree. For that we'll use ```$node->prev()```

```php
$hero = new \GraphQL\Actions\Query('hero');
echo $hero->use('name')
    ->friends(['first'=>2])
        ->use('name')
    ->prev()
    ->costumes
        ->color
    ->root()
        ->query();
```

will generate

```text
{
    hero {
        name
        friends(first: 2) {
            name
        }
        costumes {
            color
        }
    }
}
```

#### Inline Fragments

Sometimes you don't quite know the type of hero you looking for. Maybe you looking a flying Hero, maybe a Strong Hero

```php
$hero = new \GraphQL\Actions\Query('hero');
echo $hero->use('name')
    ->on('FlyingHero')
        ->use('hasCape')
    ->prev()
    ->on('StrongHero')
        ->use('strengthLevel')
    ->prev()
    ->friends(['first'=>2])
        ->use('name')
    ->prev()
    ->costumes
        ->color
    ->root()
        ->query();
```

Will generate

```text
{
    hero {
        name
        ... on FlyingHero {
            hasCape
        }
        ... on StrongHero {
            strengthLevel
        }
        friends(first: 2) {
            name
        }
        costumes {
            color
        }
    }
}
```

#### Aliases

For the element of surprise, you might need to name some of the hero's properties differently; You might want to call
friends as partners_in_good or name as call_me_this

```php
$hero = new \GraphQL\Actions\Query('hero');
echo $hero->use('name')
    ->alias('call_me_this', 'name')
    ->friends(['first'=>2])
        ->alias('partners_in_good')
        ->use('name')
    ->prev()
    ->costumes
        ->color
    ->root()
        ->query();
```

will generate

```text
{
    hero {
        call_me_this: name
        partners_in_good: friends(first: 2) {
            name
        }
        costumes {
            color
        }
    }
}

```

#### Fragments

Sorry have no super hero narrative from here :D . sticking to good old technical explanation

To use fragments declare the fragment as you would a graph and then use it within a `->use()` call as you would with a
regular property

```php
$fragment = new GraphQL\Entities\Fragment('properties', 'Hero');
$fragment->use('id', 'age');
$hero = new \GraphQL\Actions\Query('hero');
echo $hero->use('name', $fragment)->query();
echo $fragment->query();
```

will generate

```text
{
    hero {
        name
        ...properties
    }
}

fragment properties on Hero {
    id
    age
}
```

#### Variables

The use of variables feels less necessary because we're using PHP to build the query. Still...

```php
$variable = new GraphQL\Entities\Variable('name', 'String');
$hero = new \GraphQL\Actions\Query('hero', ['name' => $variable]);
echo $hero->use('name')->query();
```

will generate

```text
query getGraph($name: String){
    hero(name: $name) {
        name
    }
}
```

#### Meta fields

you can also use meta fields the same way you would request a property

```php
$variable = new GraphQL\Entities\Variable('name', 'String');
$hero = new \GraphQL\Actions\Query('hero', ['name' => $variable]);
echo $hero->use('name', '__typename')->query();
```

will generate

```text
query getGraph($name: String){
    hero(name: $name) {
        name
        __typename
    }
}
```

Which can also be aliased

```php
$variable = new GraphQL\Entities\Variable('name', 'String');
$hero = new \GraphQL\Actions\Query('hero', ['name' => $variable]);
echo $hero->use('name', '__typename')
    ->alias('type', '__typename')
    ->query();
```

will generate

```text
query getGraph($name: String){
    hero(name: $name) {
        name
        type: __typename
    }
}
```

#### Mutations

After you chose your Hero and he takes you as his sidekick he will let you do some help him with some of his daily
routine.
He might even let you choose his costume color. How cool is that?

```php
$mutation = new GraphQL\Actions\Mutation('changeHeroCostumeColor', ['id' => 'theHeroId', 'color'=>'red']);
$mutation
    ->hero
        ->use('name')
        ->costumes
            ->use('color')
    ->root()
        ->query();
``` 

Will generate

```text
mutation changeHeroCostumeColor(id: 'theHeroId', color: 'red') {
    hero {
        name
        costumes {
            color
        }
    }
}
```

With variables

```php
$mutation = new GraphQL\Actions\Mutation('changeHeroCostumeColor', ['id' => new GraphQL\Entities\Variable('uuid', 'String', ''), new GraphQL\Entities\Variable('color', 'String', '')]);
$mutation
    ->hero
        ->use('name')
        ->costumes
            ->use('color')
    ->root()
        ->query();
``` 

Will generate

```text
mutation ChangeHeroCostumeColorMutation($uuid: String, $color: String) {
    changeHeroCostumeColorAction(id: $uuid, color: $color) {
        hero {
            name
            costumes {
                color
            }
        }
    }
}
```

## Build Status

[![CircleCI](https://circleci.com/gh/cfpinto/graphql/tree/master.svg?style=svg)](https://circleci.com/gh/cfpinto/graphql/tree/master)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/fce20e3c3a194a77aca585e68fd48fc1)](https://www.codacy.com/gh/cfpinto/graphql/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=cfpinto/graphql&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/fce20e3c3a194a77aca585e68fd48fc1)](https://www.codacy.com/gh/cfpinto/graphql/dashboard?utm_source=github.com&utm_medium=referral&utm_content=cfpinto/graphql&utm_campaign=Badge_Coverage)
