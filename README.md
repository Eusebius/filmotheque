Filmothèque
===========

Author: Eusebius (eusebius@eusebius.fr)

A personal, web-based movie collection manager.

**Version 0.2.1**

Disclaimer: This is a pre-alpha version. Do not expect it to work properly. This is code under development.

**Warning for commercial users**

This application connects to "My Movie API", an interface to the IMDb website, in order to link a movie entry in Filmothèque to a movie entry at IMDb. Unlike Filmothèque, the use of My Movie API is restricted to personal purposes. If you plan to use Filmothèque for commercial purpose, you ought to deactivate the use of the API.

#Database configuration

In the provided configuration, the application expects to work on a "films" MySQL database accessible on localhost, with login and password both set to "films". You should of course change these credentials if you deploy the application on a public server.
The table generation script is at scripts/films.sql