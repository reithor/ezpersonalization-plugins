# Shopify

## Adding style

To add style in theme, go to shop backend and go to Online Store > Themes in admin menu. Click on Customize theme and then in Theme options click on Edit HTML/CSS.
In head of the layout file add line: 
```
 {{ '//your_css_file_url' | stylesheet_tag }}
```