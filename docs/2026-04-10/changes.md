# Adjustments shop.hobel.ch

## 1. Shipping Costs

- Flat rate of CHF 20.00 per order by default
- Free shipping for orders with a total value >= CHF 100
- This rule must be configurable per product (flat-rate shipping: Yes/No)
  - If No: display the text "Please contact us (email: shop@hobel.ch)"
- Create an Artisan command

## 2. Shipping Methods

- Apply "Store/Workshop Pickup" and "Shipping (Switzerland)" to all products
- Create an Artisan command

## 3. Import New Images

- New images are located in `/storage/app/image-import`
- Check if a new image is available per product; if yes, rename it and copy it to `/storage/app/public/products`
- Create an Artisan command

## 4. Inventory

- Set stock to 50 for all products
- Adjust stock in the database after a successful order
- If stock = 0, hide the "Add to Cart" widget and display the text "Product currently unavailable"

## 5. Filter Attributes (Frontend)

- Use slugs in the URL
