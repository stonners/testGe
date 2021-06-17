# October CMS BunkerData plugin

BunkerData implemented as an October CMS plugin

## Installation

Inside an October CMS project :

```sh
git clone git@bitbucket.org:bunkerpalace/octobercms-bunkerdata.git plugins/bunkerpalace/bunkerdata
php artisan plugin:refresh bunkerpalace.bunkerdata
```

## Features

- [Live switch column type](#markdown-header-live-switch-column-type)
- [Custom buttons column type](#markdown-header-custom-buttons-column-type)
- [Relation reorder behavior](#markdown-header-relation-reorder-behavior)
- [Relation renderer form widget](#markdown-header-relation-renderer-form-widget)
- [Scoreboards](#markdown-header-scoreboards)
- [Bunker Palace backend skin](#markdown-header-bunker-palace-backend-skin)
- [Four columns in backend forms](#markdown-header-four-columns-in-backend-forms)
- [Richeditor embedded partials](#markdown-header-richeditor-embedded-partials)
- [Searchable behavior](#markdown-header-searchable-behavior)

### Live switch column type

Add an ajax toggle button in backend lists for boolean values.

#### Usage

Add the `LiveSwitchController` behavior to your controller :

```php
public $implement = [
    'BunkerPalace.BunkerData.Behaviors.LiveSwitchController'
];
```

Use the `live_switch` column type wherever you want inside your model `columns.yaml` :

```yaml
active:
    label: Active
    type: live_switch
```

### Custom buttons column type

Add a dropdown with custom actions for each row in backend lists.

#### Usage

Use the `custom_buttons` column type wherever you want inside your model `columns.yaml` :

```yaml
actions:
    label: Actions
    type: custom_buttons
    button_label: Actions
    actions:
        send_newsletter:
            label: Send newsletter
            url: /.../newsletters/send
            icon: icon-envelope
        send_test_newsletter:
            label: Send test newsletter
            url: /.../newsletters/send_test
            icon: icon-envelope
```

The id of the current row record will be automatically appended to the end of the provided action url.

### Relation reorder behavior

Make a list sortable in a relation manager.

#### Usage

Add the `RelationReorderController` behavior to the controller that will be in charge of rendering the relation manager :

```php
public $implement = [
    'BunkerPalace.BunkerData.Behaviors.RelationReorderController'
];
```

Use the `relation_reorder_handle` column type in your related model `columns.yaml` to render the reorder handle  :

```yaml
sort_order:
    label: Order
    type: relation_reorder_handle
```

### Relation renderer form widget

Render a relation manager in a backend create/update form.

#### Usage

Use the `relation_renderer` form widget in your model `fields.yaml` :

```yaml
videos:
    type: relation_renderer
    tab: Videos
```

You can use the provided `relation_toolbar` partial by explicitly providing its name in your controller `config_relation.yaml` :

```yaml
videos:
    ...
    view:
        ...
        toolbarPartial: relation_toolbar
```

### Scoreboards

Custom [scoreboards](https://octobercms.com/docs/ui/scoreboard) can be rendered above forms in create/update backend pages.

#### Usage

Add a `_scoreboard.htm` template inside your controller directory.


### Bunker Palace backend skin

The provided backend skin will override some views and add custom assets to the backend.

#### Usage

Change the `backendSkin` value of the CMS configuration file in `config/cms.php` :

```php
/*
|--------------------------------------------------------------------------
| Back-end Skin
|--------------------------------------------------------------------------
|
| Specifies the back-end skin to use.
|
*/

'backendSkin' => 'BunkerPalace\BunkerData\Skins\BunkerPalace',
```

### Four columns in backend forms

You can now split backend forms in four columns instead of two initially.

#### Usage

Split fields in four columns (1/1/1/1):

```yaml
field1:
    label: field1
    span: left-left
field2:
    label: field2
    span: left-right
field3:
    label: field3
    span: right-left
field4:
    label: field4
    span: right-right
```

Split fields in 3 columns (1/1/2):

```yaml
field1:
    label: field1
    span: left-left
field2:
    label: field2
    span: left-right
field3:
    label: field3
    span: right
```

Split fields in 3 columns (2/1/1):

```yaml
field1:
    label: field1
    span: left
field2:
    label: field2
    span: right-left
field3:
    label: field3
    span: right-right
```

### Richeditor embedded partials

This pretty neat feature allows you to embed partials in any richeditor field in backend.

#### Usage

Add the `embedPartials` button to your richeditor field config.

Declare an array of partials the user will be allowed to embed in the `embedPartials` option.

Each partial item has an arbitrary key and must have at least these two properties :

`partial` : the partial to render (filename minus `.htm`)  
`name` : the text displayed in the richeditor dropdown selector and in the placeholder block

```yaml
body:
    label: Body
    type: richeditor
    toolbarButtons: embedPartials
    embeddedPartials:
        my-partial: 
            name: "My partial"
            params: 
                ids:
                    - 1
                    - 2
                    - 3
                name: 'Clownpenis'
```

The optional params property can be used to provide variables to be used by the rendered partial.

#### Rendering a field containing embedded partials

You can use the `parseEmbeddedPartials` Twig filter to render the partials embedded in your fields content :

```html
{{ body|parseEmbeddedPartials }}
```

### Searchable behavior

WIP
