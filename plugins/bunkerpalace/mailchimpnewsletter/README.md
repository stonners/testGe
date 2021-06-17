# October CMS Mailchimp Newsletter plugin

## Installation

Inside an October CMS project :

```sh
git clone git@bitbucket.org:bunkerpalace/october-cms-mailchimp-newsletter.git plugins/bunkerpalace/mailchimpnewsletter
cd plugins/bunkerpalace/mailchimpnewsletter && composer install
php artisan plugin:refresh bunkerpalace.mailchimpnewsletter
```

## Configuration

Go to "Settings" > "Paramètres de la newsletter", paste your Mailchimp API key and configure to your needs.

## Usage

### newsletterSubscribe component

This component will show 

Add the component default partial to your page or layout by including it with :

```html
{% component 'newsletterSubscribe" %}
```

### newsletter component

More documentation soon...