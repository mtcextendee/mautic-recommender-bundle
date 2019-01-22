![image](https://user-images.githubusercontent.com/462477/51494736-0fefb100-1dba-11e9-8d44-27a24292e3dd.png)


# Mautic Recommender Bundle 
### Mautic for e-commerce

The first product recommendations system to Mautic.  Increase your customer satisfaction and spending with product recommendations. Applicable to your home page, product detail, cart page, emailing campaigns, dynamic ocntent and much more. Quick and Easy

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
3. `php app/console doctrine:schema:update --force`

Then go to to plugins and enable Recommender. You should see new menu item Recommender

![image](https://user-images.githubusercontent.com/462477/51552847-ce224180-1e71-11e9-920b-f68d7da155eb.png)

## Usage

### Events
You can track interactions between your contacts and items. Create your custom events like add to cart, purchase, detailview, add to wishlist, rating.... Weight option use for grading the priority of each event.   

![image](https://user-images.githubusercontent.com/462477/51553290-bf885a00-1e72-11e9-909b-eef4f8a751f5.png)

### Templates

Build custom theme form display items in email or website.

![image](https://user-images.githubusercontent.com/462477/51553414-1726c580-1e73-11e9-90a8-b853827d496c.png)

### Recommenders

Recommenders configure what product will display to cour contacts. Then you will use recommender tokens in emails or dynamic content (for example `{recommender=1}`)

![image](https://user-images.githubusercontent.com/462477/51553766-eeeb9680-1e73-11e9-91e1-a3e77c71dbc5.png)

## Tracking

### Ccontacts 

Use standard contact tracking by Mautic ([see contact monitoring docs](https://www.mautic.org/docs/en/contacts/contact_monitoring.html))

### Import items

Items are parsed from your external JSON file. This command should run initial, but also you can update your items one time each 15 minutes.

```shell
php app/console mautic:recommender:import --type=items`  
`--file="http://domain.tld/pathto/items.json"
```

Options:

- --batch-limit=50 (default)
- --timeout='-1 day' (default) timeout before update product

## Tracking events by JS

Require Mautic standard tracking code to website (right after `<body>`)

You need to add Mautic tracking code  [to your website](https://www.mautic.org/docs/en/contacts/contact_monitoring.html#javascript-js-tracking)

```js
mt('send', 'RecommenderEvent', { eventName: 'DetailView', itemId:'9-191' });
```

- eventName - required
- itemId - required

You can also to each events set your own parameters (price, profit, quantity...). Example:
 
```js
mt('send', 'RecommenderEvent', { eventName: 'Cart', itemId:'9-191', price: '39', quantity: '2', profit: '9' });
```

## Import events by JSON

```json
[
  {
    "itemId": 13807,
    "eventName": "purchase",
    "contactEmail": "customer@domain.tld",
    "price": 41,
    "profit": 19,
    "dateAdded": "2018-12-24 11:12"
  },
  {
    "itemId": 13807,
    "eventName": "addToCart",
    "contactEmail": "customer2@domain.tld",
    "price": 19,
    "profit": 6,
    "dateAdded": "2019-01-11 09:12:31"
  }
]
```




## Delivery recommended items to users

#### 1. Create Recommender template
 
Go to Components > Recommender and create TWIG supported templates. Recommender template use in supported channels. 

Then use in content of support channels tag `{recommender=1}` (replace number with your Recommender template ID)

At the moment Recommender integration support 

- Email
- Focus
- Dynamic Content
- Push notifications