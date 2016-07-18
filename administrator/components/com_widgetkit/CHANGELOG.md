# Changelog

### 2.7.4
 - Fixed error in slider- & parallax-widget
 - Fixed broken twitter post ids

### 2.7.3
 - Fixed PHP 5.3 compatibility
 - Fixed map widget warnings

### 2.7.2
 - Added support for @3x density images (supported by iPhone 6+)
 - Added placeholder image as fallback in WooCommerce provider (WP)
 - Added iframe size options into custom content provider
 - Added parallax min-width option
 - Fixed link required to enable overlay in grid-stack widget
 - Fixed wrong image path with nice URLs (WP)
 - Fixed custom map marker image src
 - Fixed maps cluster icon sources
 - Fixed disappearing widgetkit button (WP)

### 2.7.1
 - Fixed preview within media picker (J)
 - Fixed use of deprecated function add_object_page() (WP)
 - Fixed images invisible in gallery, grid and grid-slider widgets
 - Fixed slideshow-panel autoplay pauseOnHover

### 2.7.0
 - Added content rss content provider
 - Added custom field 'date'
 - Added custom field 'pathpicker'
 - Added save place information if using google autocompleter for maps
 - Fixed warning in Instagram content provider when open_basedir is used
 - Fixed media picker selecting videos (J)
 - Fixed loading article tags for filter function also when setting is disabled (J)
 - Fixed instagram caching error of emoticons

### 2.6.5
 - Updated UIkit to 2.26.2

### 2.6.4
 - Fixed ZOO/K2 plugins not loading

### 2.6.3
 - Added customizable map marker
 - Added gutter large option
 - Fixed switcher not loaded in Zoo content provider
 - Fixed gallery / grid / grid-slider overlapping images wrong selector
 - Fixed default mapping in K2 content provider
 - Fixed mapping of K2 extra fields
 - Fixed instagram content provider for php 5.3
 - Fixed Notice when using random sorting in folder content

### 2.6.2
  - Fixed Joomla content provider with missing tags property


### 2.6.1
 - Added media2 image to Joomla content provider
 - Fixed items not sortable in custom content provider

### 2.6.0
 - Added instagram content provider
 - Added HTML editor field
 - Added Joomla 3.5 compatibility
 - Added map widget directions text into language file
 - Added textfield for sorting filter tags
 - Changed content field and gallery lightbox content to HTML editor
 - Moved Font Awesome icon files to media folder (J)
 - Fixed custom content editing for iPad
 - Fixed gallery / grid / grid-slider dynamic grid overlapping images

### 2.5.3
 - Fixed truncate helper warning

### 2.5.2
 - Fixed truncate helper function
 - Fixed PHP 5.3 compatibility

### 2.5.1
 - Added truncate helper function
 - Fixed resize images for slideshow modal in gallery
 - Fixed map get direction for marker set to "show" only
 - Fixed ZOO GoogleMaps API warning if location is empty

### 2.5.0
 - Added custom content required fields
 - Added required fields map & popover widget
 - Added slideshow-panel widget
 - Added switcher-panel widget
 - Added popover widget
 - Added list widget
 - Added new slideshow modal to gallery
 - Added panel style sequence option in grid widget
 - Added media2 to core custom fields
 - Added alternative lightbox image to gallery
 - Added contrast color option to parallax
 - Added alternative content field for the lightbox to gallery
 - Fixed gallery lightbox width / height settings
 - Fixed media 'auto' width / height settings
 - Fixed map get direction without content
 - Fixed caching resized images (WP)

### 2.4.8
 - Removed doubled content field in WP content provider (WP)
 - Fixed image file format case sensitive (J)
 - Fixed show template specific widgets only (J)

### 2.4.7

 - Moved image cache to /media (J)

### 2.4.6

 - Added dropdown.less, fixes responsive tabs
 - Updated maps widget: get directions link always opens in new tab/window
 - Removed include redundant subcategories checkbox in Joomla content provider
 - Fixed caching thumbnails images
 - Updated language file

### 2.4.5

 - Fixed ZOO item field mapping
 - Fixed encoding blanks in image filenames
 - Fixed prevent double_encoding htmlspecialchars in image src

### 2.4.4

 - Added missing language strings
 - Fixed Folder Content provider issue on non GNU systems
 - Fixed Folder Content provider issues with file titles

### 2.4.3

 - Added images from folders are loaded via add media
 - Added Joomla category multi selection
 - Added Joomla modified order option
 - Added max images option to folder content
 - Added autoplay options to slider
 - Updated content provider markup
 - Fixed K2 image source
 - Fixed slideset filter options
 - Fixed widget selection in the frontend
 - Fixed different protocols in the link
 - Fixed widget settings merge
 - Fixed ZOO/K2/Joomla Item/Articles links

### 2.4.2

 - Fixed sorting items in custom content provider

### 2.4.1

 - Added support for shortcodes in custom content

### 2.4.0

 - Added Twitter content provider
 - Added widget type filter to list view
 - Added option for kenburns animation to slideshow
 - Changed click events to anchors at map widget

### 2.3.1

 - Fixed ZOO & K2 mapping (date, author, categories)

### 2.3.0

 - Added slideset widget
 - Added slider widget
 - Added parallax widget
 - Added folder content provider
 - Added filter option to show all items
 - Added option to use a second image as overlay to widgets
 - Added option to open all links in a new window
 - Added option to close first item initially to accordion
 - Added option for the kenburns duration to slideshow
 - Added option for the content text size to slideshow
 - Added breakpoint option to grid stack
 - Added vertical gutter option to grids
 - Added button link option to all widgets
 - Added gutter medium option to grid widgets
 - Added responsive tabs for filter nav
 - Added meta data support for content providers to grid widget
 - Added ZOO reversed ordering
 - Fixed media breakpoint option in all widgets
 - Fixed button functionality when creating a new widget
 - Fixed content keeping when switching the widget-type of an unsaved widget
 - Fixed HTML tags for image alt attribute
 - Fixed maps.js for IE10/11
 - Fixed ZOO ordering

### 2.2.1

 - Added active state for selected widgets
 - Fixed relative URL conversion

### 2.2.0

 - Reworked admin UI by coupling widget settings and content
 - Moved widget settings from shortcode to database
 - Added copy functionality for widgets

### 2.1.5

 - Added escaping of content link and social links
 - Fixed email cloaking conflict (J)

### 2.1.4

  - Added dotnav to nav options in switcher
  - Added support to create a slideshow without media element
  - Added ZOO item reference in item object
  - Fixed PHP notice in gallery
  - Fixed missing first folder in Joomla media picker
  - Fixed image paths in maps widget
  - Fixed ZOO edit view fields reseting issue

### 2.1.3

  - Added last option for media position in switcher
  - Fixed Zoo mapping issues
  - Fixed 1-Click Updates issues

### 2.1.2

  - Added multiple media select with shift-key (J)
  - Added filter sorting + uppercase for filter words
  - Added title auto-format after add media
  - Updated responsive behavior of grid-stack
  - Fixed missing language folder (J)
  - Fixed heading margin-bottom in gallery
  - Fixed customizer in WordPress
  - Fixed double animation parameter in grid-slider
  - Fixed get item option for php 5.3

### 2.1.1

  - Added title and title size option to accordion
  - Updated custom field override priority
  - Fixed navigation position option in switcher
  - Fixed social button alignment in all widgets
  - Fixed lightbox link in gallery
  - Fixed jumping to top of the page when clicking the Widgetkit button
  - Fixed multiple media fields and its meta data
  - Fixed thumbnail calculation in slideshow
  - Fixed broken Joomla cache for ZOO (J)
  - Fixed autofocus for content title


### 2.1.0

  - Added translation support
  - Added gallery widget with lightbox
  - Added grid-slider widget
  - Added accordion widget
  - Added dynamic grid and filter options to grid widget
  - Added new overlay and image animations to all widgets
  - Added image resize options to all widgets
  - Added option to use an alternative image as thumbnail
  - Added support for CKEditor
  - Added Widgetkit shortcodes for text widgets (WP)
  - Added custom field mappings for content providers (WordPress, WooCommerce, Joomla, Zoo, K2)
  - Added marker cluster option for map widget
  - Added badge field to grid widgets
  - Added better overlay link to all widgets
  - Added support for PCRE versions pre 7.0
  - Updated all widgets according to UIkit 2.17.0
  - Optimized all widget options
  - Fixed zoomwheel setting for map widget
  - Fixed directions setting for map widget

### 2.0.7

  - Fixed adding Video URLs to Custom Content Type

### 2.0.6

  - Added ZOO link mapping
  - Added better support for ZOO plugin 3rd party integrations

### 2.0.5

  - Added selected Widget indicator in the Widget/Module buttons
  - Updated UIkit to 2.16.2
  - Fixed another path issue on installations located in root

### 2.0.4

  - Added improved default item name (J)
  - Fixed default item name on media select
  - Fixed Google Maps search results z-index
  - Fixed ZOO Content Categories list display issue
  - Fixed path issue on installations located in root

### 2.0.3

  - Fixed editor button

### 2.0.2

  - Added content view modes and filter
  - Added Google Maps API lazy loading
  - Added Advanced Module Manager compatibility
  - Added link none option for Grid, Grid-Stack and Switcher
  - Added RokPad Editor support
  - Added error notifications when uploading media (J)
  - Added image option to Joomla content plugin (J)
  - Fixed routing issues
  - Fixed Vimeo media parameters
  - Fixed slideshow nav hidden on touch devices
  - Fixed margin in modal when editing content (J)
  - Fixed Grid-Stack text align on small devices
  - Fixed incompatibility with older Composer versions
  - Fixed overlay if media has rounded border for Grid, Grid-Stack and Switcher

### 2.0.1

  - Added site styles/scripts caching
  - Added featured filter for Joomla content (J)
  - Added JCE editor compatibility

### 2.0.0

  - Initial Release
