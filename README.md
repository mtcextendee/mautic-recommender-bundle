# MauticRecommenderBundle

The first product recommendations system to Mautic.  Increase your customer satisfaction and spending with product recommendations. Applicable to your home page, product detail, cart page, emailing campaigns and much more. Quick and Easy

Sign in for news: [mtcextendee.com](https://mtcextendee.com/)

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

## Contacts

Standard contact tracking by Mautic ([see contact monitoring docs](https://www.mautic.org/docs/en/contacts/contact_monitoring.html))

## Items

Recommender combine data about items and contacts and related data between both.Then before start working with Recommender we need import items. 

### Import items

Items are parsed from your external JSON file. This command should run initial, but also you can update your items one time per 24/48 hours.

`php app/console mautic:recommender:import --type=items`  
`--file="http://domain.tld/pathto/items.json"`

## Tracking events by JS

Require Mautic standard tracking code to website (right after `<body>`)

First you have to add Mautic tracking code  [to your website](https://www.mautic.org/docs/en/contacts/contact_monitoring.html#javascript-js-tracking)


`mt('send', 'RecommenderEvent', { eventName: 'DetailView', itemId:'9-191' });`

- eventName - required
- itemId - required

You can also to each events set your own parameters (price, profit, quantity...). Example for cart:
 
`mt('send', 'RecommenderEvent', { eventName: 'Cart', itemId:'9-191', price: '39', quaintity: '2' });`


## Delivery recommended items to users

#### 1. Create Recommender template
 
Go to Components > Recommender and create TWIG supported templates. Recommender template use in supported channels. 

Then use in content of support channels tag `{recommender=1}` (replace number with your Recommender template ID)

At the moment Recommender integration support 

- Email
- Focus
- Dynamic Content
- Push notifications