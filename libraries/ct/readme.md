# About Custom Tables #

Custom Tables (CT) is a WordPress developers toolkit to handle custom database table workflow similar to WordPress CPT.

Check [example.php](./example.php) file included on this project to see a few examples to get it working.

CT has been developed as internal library for [GamiPress](https://gamipress.com) plugin in order to bring to GamiPress's logs and user earnings tables the same features as WordPress post types (admin UI, cached query, rest API endpoints, etc).

Important: CT public API is in development phase, this means current version is unstable and much of the current features will change. To use this library on live project be sure you know you are doing!

Contributions are really appreciated! Looking for help to standarize functions and hooks as well as for documentation.

## Features (work in progress) ##

Custom table registration:

- [x] Custom table registration (like registering a WordPress post type)
- [x] Automatic table creation if not exists
- [x] Easy field definition
- [x] Schema parser
- [x] Automatic schema updater (yay!)
- [x] Database parameters (collate, engine, etc)
- [x] Ability to show or hide from admin UI (disable UI for a desired table)
- [x] Custom Capabilities (with support for administrators)
- [x] Meta data functionality
- [x] Query class to handled cached queries (like WP_Query but for custom tables)
- [x] Rest API support (custom table and meta data)

List view (with features similar to WP tables):

- [x] Pagination
- [x] Search
- [x] Sortable Columns
- [x] Bulk actions
- [x] User screen settings
- [x] List view views
- [ ] Trash functionality?
- [ ] Revisions functionality?
- [x] Delete Permanently action

Edit View (similar to WP edit screen):

- [x] Meta boxes
- [x] Screen options
- [x] Show hide Meta boxes
- [x] Allow user to toggle view columns
- [x] Allow define edit view columns (to force to 1 column)
- [x] Delete Permanently action

Other features

- [x] CMB2 support
- [ ] Documentation (help wanted!)
- [x] Add WP Lib Loader to always load the newest version (http://jtsternberg.github.io/wp-lib-loader/)

## Plugins ##

- [Ajax List Table](https://github.com/rubengc/ct-ajax-list-table): Utility to render a Custom Tables (CT) List Table with ajax searching and pagination.
- [Rest API Docs](https://github.com/rubengc/ct-rest-api-docs): Rest API docs generator for Custom Tables (CT).

## Changelog ##

**1.0.7**

* **Bug Fixes**
* Fixed PHP notices caused by add_submenu_page() function when passing null as first parameter.

**1.0.6**

**Improvements**
* Added more nonce checks to prevent CSRF attacks.

**1.0.5**

**Improvements**
* Added more nonce checks to prevent CSRF attacks.

**1.0.4**

**Improvements**
* Reduced the number of "Show tables" queries.

**1.0.3**

**Improvements**
- Added support for CMB2 fields data removal if field has "multiple" set to "true".

**1.0.2**

**Bug Fixes**
- Make use of the min() function when defining length of the table keys (thanks to @mholubowski).

**1.0.1**

**Improvements**
- Prevent to add index length for DATETIME fields (thanks to @mholubowski, fixes #9).
- Quote all fields and indexes during database creation.

**1.0.0**
Initial release.