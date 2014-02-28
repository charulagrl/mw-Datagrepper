# mw-Datagrepper

A MediaWiki extension that enable you to embed html cards from Datagrepper app on your user page. i.e. the last 5 messages found in datagrepper

## What does it provide?

mw-FedoraBadges provides one
[parser function](https://www.mediawiki.org/wiki/Manual:Parser_functions):

### `{{ #datagreppermessages : username }}`

This displays last 5 messages found in datagrepper on $username's page. 

## How do I enable it?

Clone this repository to `extensions/Datagrepper`, then in `LocalSettings.php`,
add the following line:

```
require_once "$IP/extensions/Datagrepper/Datagrepper.php";
```
In order to add CSS download the Mediawiki [Extension: CSS](http://www.mediawiki.org/wiki/Extension:CSS).

## License

MIT License. (c) 2013 Red Hat, Inc.
Written by Ricky Elrod.



