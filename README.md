# MauticRecommenderBundle

Increase your customer satisfaction and spending with Amazon and Netflix-like AI powered recommendations. Applicable to your home page, product detail, emailing campaigns and much more. Quick and Easy Integration into Your Environment.

Sign in for news: [mtcrecommender.com](https://mtcrecommender.com/)

## What plugin do?

- Recommendations items based on contact interactions
- Abandoned cart
- Frequently bought together
- Customers who viewed this item also viewed
- Special offers and product promotions
- Related to this item
- Customers who bought items in your cart also bought
- Recently viewed items and featured recommendations
- Category best sellers
- Most wished products

## Which channels do you support?

- Emails
- Focus (popups)
- Dynamic content
- Web Push notification

## Create Recommender account for free

1. Go to  [www.recommender.com](https://www.recommender.com)  and create account.  
2. Free plan up to 100 000 recommendation requests is good choice.  
3. Then go to Accounts > Organizations > your organization > edit database and copy API credits

![](https://docs.mtcextendee.com/assets/images/image03.jpg?v13024233387251)

## Import data (items, users)

Recommender combine data about items and user and related data between both.  
Then before start working with Recommender we need import items (required) and contacts (optional).  
Items import

### Import items

Items are parsed from your external JSON file. This command should run initial, but also you can update your items one time per 24/48 hours.

`php app/console mautic:recommender:import --type=items`  
`--file="path/to/items.json"`

Results from command

![](https://docs.mtcextendee.com/assets/images/image02.jpg?v13024233387251)

### Import contacts

Contacts are imported from Mautic contacts.  
If you are working on new Mautic installation, then you can skip this step. Contacts import is initial and you should run it first time. Then Mautic will send data about contacts realtime.  
  
Command:

`php app/console mautic:recommender:import --type=contacts`

Results from command

![](https://docs.mtcextendee.com/assets/images/image01.jpg?v13024233387251)

## Send data realtime by API

Plugin for Woocommerce https://github.com/kuzmany/woo-mautic-recommender

You can send based interactions between items/user by API.  
You have to setup  [Mautic API](https://github.com/mautic/api-library).  
Based init code looks like:

`$api = new MauticApi();`  
`$apiRequest = $api->newApi('api', $auth, $apiUrl);`

Interactions

AddCartAddition

Adds a cart addition of a given item made by a given user.

`$component = 'AddCartAddition';`  
`$options = ['userId' => 1, 'itemdId' => 1, 'amount'=>1, 'price'=>99];`  
`$apiRequest->makeRequest('recommender/'.$component, $options, 'POST');`

DeleteCartAddition

Adds a cart addition of a given item made by a given user.

`$component = 'DeleteCartAddition';`  
`$options = ['userId' => 1, 'itemdId' => 1];`  
`$apiRequest->makeRequest('recommender/'.$component, $options, 'POST');`

AddPurchase

Adds a purchase of a given item made by a given user.

`$component = 'AddPurchase';`  
`$options = ['userId' => 1, 'itemdId' => 1,`  
`'amount' => 1, 'price' => 99, 'profit' => 9];`  
`$apiRequest->makeRequest('recommender/'.$component, $options, 'POST');`

DeletePurchase

Deletes an existing purchase

`$component = 'DeletePurchase';`  
`$options = ['userId' => 1, 'itemdId' => 1];`  
`$apiRequest->makeRequest('recommender/'.$component, $options, 'POST');`

AddDetailView

Adds a detail view of a given item made by a given user.

`$component = 'AddDetailView';`  
`$options = ['userId' => 1, 'itemdId' => 1];`  
`$apiRequest->makeRequest('recommender/'.$component, $options, 'POST');`

## Send data realtime by JS API

Add Mautic tracking code to website

First you have to add Mautic tracking code  [to your website](https://www.mautic.org/docs/en/contacts/contact_monitoring.html#javascript-js-tracking)

Then edit your tracking pixel on each product page with Recommender code to pageview event. Data send by pixel improve personalization products for your contacts. Example how to add custom parametrs to Mautic pageview event:

**AddDetailView**

Adds a detail view of a given item made by a given user.

`mt('send', 'pageview', { Recommender: '{"AddDetailView":{"itemId":1}}' });`

## Delivery recommended items to users

#### 1. Create Recommender template
 
Go to Components > Recommender and create TWIG supported templates. Recommender template use in supported channels. 

Then use in content of support channels tag `{recommender=1}` (replace number with your Recommender template ID)

At the moment Recommender integration support 

- Email
- Focus
- Dynamic Content
- Push notifications

#### 2. Create campaign actions

##### Recommender Email

<a href="https://user-images.githubusercontent.com/462477/42328412-77398ed8-806e-11e8-9b93-f1137b455120.png" target="_blank"><img alt="Recommender Email" width="400" src="https://user-images.githubusercontent.com/462477/42328412-77398ed8-806e-11e8-9b93-f1137b455120.png"></a>

##### Recommender Focus

<a href="https://user-images.githubusercontent.com/462477/42328482-a2630f26-806e-11e8-8877-57b35169cddc.png" target="_blank"><img alt="Recommender Focus" width="400" src="https://user-images.githubusercontent.com/462477/42328482-a2630f26-806e-11e8-8877-57b35169cddc.png"></a>

##### Recommender Dynamic Content

<a href="https://user-images.githubusercontent.com/462477/42802343-f02d632c-89a2-11e8-824f-9d6c77e87626.png" target="_blank"><img alt="Recommender Dynamic Content" width="400" src="https://user-images.githubusercontent.com/462477/42802343-f02d632c-89a2-11e8-824f-9d6c77e87626.png"></a>
 
 Both campaign action support 3 types of recommendations:
 
 - Recommendations based on interactions
 - Abandoned cart
 - Advanced (with [filter and booster](https://docs.recommender.com/reql_filtering_and_boosting.html) support) 
 
 ## How to work recommendations types?
 
 1. Recommendations
 
Based on user-item interactions.
 
 2. Abandoned cart
 
 Based on Add cart addition and Add purchase interactions.  Plugin display items based on date added contact to campaign and date added cart addition.  If contact date added cart addition is greather as contact date added to campaign the items  will displayed until purchase. 
 
 3. Advanced
 
 You can use filter and booster for display recommendations. Read [docs](https://docs.recommender.com/reql_filtering_and_boosting.html)
