# Silverstripe Janolaw

## Introduction
The module grant you access to the API provided by the Janolaw AG. This allows
you to import the contents of the documents you created within your user account
at Janolaw:

* Legaldetails
* Terms
* Revocation
* Datasecurity
* Model Withdrawal-Form

## ToDo
- Fetch Documents

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
4. After saving your settings you also get the Versionnumber the API for your Account are running on

### Content Pages
1. Create your desired Page (see above)
2. Edit the page and go to the "Documents" tab
3. Now your able to define which Data Types you want to retrieve from the API by activating the corresponding Type
4. For the Text and PDF Data Types your also able to define a Filename, which will be used to save the file under the "assets/Uploads" folder on your server
