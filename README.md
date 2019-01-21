# Mautic Recommender Bundle for e-commerce

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

## How it works

Recommender combine data about items and contacts and related data between them. That means:
 - you should track your contacts like before
 - you should import items to Mautic
 - you should track interactions between your contact's and items (detail view, add to cart, purchase etc.)
 
### Features
- Segments filtering based on contacts and items interactions
- Flexible items builder and template system 
- Custom filter for display related product to each contact's
- Custom tracking events (you can track whatever you want)
- Custom additional data to you tracking events
- Multi channel support
- [Google Analytics integration](https://github.com/kuzmany/mautic-extendee-analytics-bundle) (in development)

## Setup  

## Install plugin 

Run

1. composer require kuzmany/mautic-recommender-bundle
2. php app/console mautic:plugins:reload
3. Go to plugins and enable Recommender

### Track your contacts 

Still using standard contact tracking by Mautic ([see contact monitoring docs](https://www.mautic.org/docs/en/contacts/contact_monitoring.html))

### Import items

Items are parsed from your external JSON file. This command should run initial, but also you can update your items one time each 15 minutes.

`php app/console mautic:recommender:import --type=items`  
`--file="http://domain.tld/pathto/items.json"`

Options:

- --batch-limit=50 (default)
- --timeout='-1 day' (default) timeout before update product

## Tracking events by JS

Require Mautic standard tracking code to website (right after `<body>`)

You need to add Mautic tracking code  [to your website](https://www.mautic.org/docs/en/contacts/contact_monitoring.html#javascript-js-tracking)


`mt('send', 'RecommenderEvent', { eventName: 'DetailView', itemId:'9-191' });`

- eventName - required
- itemId - required

You can also to each events set your own parameters (price, profit, quantity...). Example:
 
`mt('send', 'RecommenderEvent', { eventName: 'Cart', itemId:'9-191', price: '39', quantity: '2', profit: '9' });`


## Delivery recommended items to users

#### 1. Create Recommender template
 
Go to Components > Recommender and create TWIG supported templates. Recommender template use in supported channels. 

Then use in content of support channels tag `{recommender=1}` (replace number with your Recommender template ID)

At the moment Recommender integration support 

- Email
- Focus
- Dynamic Content
- Push notifications