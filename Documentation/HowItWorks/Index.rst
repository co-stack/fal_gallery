How It Works
============

Short'n'quick
-------------

Defaults (first visit on the Page):

1. The Single View will show the default image.
2. The List View will show all images from the selected folder.
3. The Gallery View will show the first image of each subfolder.

Navigation (collaboration between Views):

* When clicking on a Thumbnail in the Gallery View:

  * The Gallery View will set the new root folder to the folder of the image that has been clicked
  * The List View will show the images of the new Gallery View's folder
  * The Single View will not change

* When clicking an a Thumbnail in the List View:

  * CASE1: Lightbox URL disabled

    * The Gallery View will not change
    * The List View will not change
    * The Single View shows the original image of the Thumbnail that has been clicked

  * CASE1: Lightbox URL enabled

    * Nothing will change
    * YOUR Javascript takes care of the link event (opening the lightbox)

* Images in the Single View can't be clicked

Long and detailed
-----------------

To be written. This needs a lot of time to make screenshots showing the mechanics.
