# Silverstripe Janolaw

## Introduction
The module grants you access to the API provided by the Janolaw AG. This allows
you to import the contents of the documents you created within your user account
at Janolaw:

* Legaldetails
* Terms
* Revocation
* Datasecurity
* Model Withdrawal-Form

## ToDo
At the moment only fetching the contents in html format are supported.

## Requirements
SilverStripe 4+

## Installation Instructions

### Composer
1. ```composer require 6fdigital/silverstripe-janolaw```
2. Visit http://yoursite.com/dev/build?flush=1 to rebuild the database.

### Manual
1. Place this directory in the root of your SilverStripe installation, rename the folder to 'janolaw'.
2. Visit http://yoursite.com/dev/build?flush=1 to rebuild the database.

## Usage
### Settings
1. Visit the settings under Settings > Janolaw
2. Provide the User and ShopID
3. [Optional] Provide the default cache time for the content in hours (defaults to 8h)
4. Now chose your pages for the appropriate content-type