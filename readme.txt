=== Hostel ===
Contributors: prasunsen
Tags: hostel, hotel, booking, bnb, rooms, wpmu, touch
Requires at least: 3.3
Tested up to: 3.9.1
Stable tag: trunk
License: GPL2

Create your hostel, small hotel or BnB site with WordPress. Manage rooms, booking, unavailable dates, and more. 

== License ==

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

== Description ==

Create your hostel, small hotel, or BnB site with Wordpress:

###Features###

- Manage your booking mode: accept paypal, manual payments, or don't allow online booking
- Manage email notifications
- Manage rooms, beds, and prices
- Set unavailable dates when you are on vacations or just don't want to accept guests in some rooms
- Manage bookings, process payments, contact customers
- List your rooms by using shortcodes

There are more and better features + premium support in the PRO version. Check it on our new site: [wp-hostel.com](http://wp-hostel.com "wp-hostel.com") 

###Shortcodes###

- [wphostel-list] will display a table with your available rooms. A date selector on the top lets the user choose dates of their visit and then the rooms list is updated. If you have enabled booking in your Hostel settings page, the table will also show "Book" button when appropriate. The button will automaically load the booking form.

- [wphostel-booking] displays a generic booking form with a drop-down selector for choosing room, and a date selector. If you use the [wphostel-list] shortcode you most probably do not need this one because the booking form is automatically generated.

For translating the plugin check the Help page under the Hostel menu in your administration.

###Community Translations###

The following translations are currently available. Please note they are maintained by volunteer translators and we can't guarantee their accuracy.

Spanish: [wphostel-es_ES.mo](http://backpackercompare.com/wp-content/uploads/2014/06/wphostel-es_ES.mo "wphostel-es_ES.mo") | [wphostel-es_ES.po](http://backpackercompare.com/wp-content/uploads/2014/06/wphostel-es_ES.po "wphostel-es_ES.po")

== Installation ==

= Installation =

1. Unzip the contents and upload the entire `hostel` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "Hostel" in your menu and manage the plugin

= Getting Started =

1. Go to Hostel link in your admin menu to manage your rooms and rates
2. Use the shortcodes to install a list of your rooms or to add the booking code to a post or page where you have described your rooms
3. Set up unavailable dates if you have such

== Frequently Asked Questions ==

None yet, please ask in the forum

== Screenshots ==

1. The options page let you set up currency, booking mode, and email settings

2. You can add any number of private and dorm rooms, specify price, bathroom etc

3. Add/Edit booking. As admin you can review and edit bookings made from users and manually add bookings made by phone or email

4. If there are any dates when your property or some rooms are not available, add them here

5. The Help page shows the available shortcodes.

== Changelog ==

= Version 0.8.2 =
- Changed the booking form design to avoid styling issues

= Version 0.8 = 
- Reworked all forms to work only with Ajax. This will let you use multiple 
- Removed the requirement and setting for booking form URL. This is no longer needed
- Improved the booking form validations
- "Per room" price is now available. When this is selected number of beds become irrelevant because your guests are booking the entire room.
- Fixed bug: tables were not properly created on installation
- Setting a custom currently is available
- Added ajax loading of the beds in the booking form to prevent confusing numbers on the private rooms.
- Fixed bug in [wphostel-book] shortcode

= Version 0.7 =

- Added "wphostel-book" shortcode which allows you to place a booking button on any page (usually on a page where you have described your room manually, with pictures etc)
- Added a validaion on the [wphostel-list] so no more than 5 days interval can be selected (to avoid creating long ugly tables with rooms). Setting soon to be made configurable.
- Added zebra tables in manage bookings and manage rooms pages
- Changed the date drop-downs on the front end to use the date picker
- Major improvements of the availability logics, differentiating between dorms and private rooms
- Fixed bug with resetting the room type on editing
- Fixed HTML content-type of the auto-mails
- Fixed bug with pending status when manually marking booking as paid
- Fixed JS validation error on the [wphostel-list] shortcode
- Fixed problem with overlapping the "to" day when booking and showing availability
- Fixed issues with unavailable dates: when date is unavailable, all beds should be considered unavailable

= Version 0.5.9 =

First public release