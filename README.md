# graphql
A simple yet powerful GraphQL query builder

## How it works?

Writing GraphQL queries can be a painful process. With GraphpQL query builder write PHP and get GraphQL

### Examples

When finding an Hero to be his sidekick one finds himself overwhelmed with the number of Heros out there.
There are all sort of Heros so lets list them all

#### Fields

```php
$hero = new GraphQL\Graph('hero');
echo $hero->use('name')
    ->friends
        ->use('name')
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
$hero = new GraphQL\Graph('hero');
echo $hero->use('name')
    ->friends(['first'=>2])
        ->use('name')
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
Sometimes you might need to know more about this Hero, like when you want to know a hero friends and costumes. We then need to go back in the Hero tree. For that we'll use ```$node->prev()``` 

```php
$hero = new GraphQL\Graph('hero');
echo $hero->use('name')
    ->friends(['first'=>2])
        ->use('name')
    ->prev()
    ->costumes
        ->color
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
$hero = new GraphQL\Graph('hero');
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

#### Mutations
After you chose your Hero and he takes you as his sidekick he will let you do some help him with some of his daily routine.
He might even let you choose his costume color. How cool is that?

```php
$mutation = new GraphQL\Mutation('changeHeroCostumeColor', ['id' => 'theHeroId', 'color'=>'red']);
echo $mutation
    ->hero
        ->use('name')
        ->costumes
            ->use('color')
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

#### Aliases
For the element of surprise, you might need to name some of the hero's properties differently; You might want to call 
friends as partners_in_good or name as call_me_this

```php
$hero = new GraphQL\Graph('hero');
echo $hero->use('name')
    ->alias('call_me_this', 'name')
    ->friends(['first'=>2])
        ->alias('partners_in_good')
        ->use('name')
    ->prev()
    ->costumes
        ->color
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
Sorry have no super hero narrative for this :D . sticking to good old technical explanation

To use fragments declare the fragment as you would a graph and then use it within a `->use()` call as you would with a regular property

```php
$fragment = new GraphQL\Fragment('properties', 'Hero');
$fragment->use('id', 'age');
$hero = new GraphQL\Graph('hero');
echo $hero->use('name', $fragment)->query();
```
will generate
```text
{
    hero {
        name,
        ...properties
    }
}

fragment properties on Hero {
    id
    age
}
```

### Coming Soon
#### Variables
#### Directives
#### Meta fields
## Support on Beerpay
Hey dude! Help me out for a couple of :beers:!

[![Beerpay](https://beerpay.io/cfpinto/graphql/badge.svg?style=beer-square)](https://beerpay.io/cfpinto/graphql)  [![Beerpay](https://beerpay.io/cfpinto/graphql/make-wish.svg?style=flat-square)](https://beerpay.io/cfpinto/graphql?focus=wish)
