  <a href='https://ko-fi.com/1day2die' target='_blank'><img height='50' style='border:0px;height:35px;' src='https://ko-fi.com/img/githubbutton_sm.svg' border='0' alt='Buy Me a Coffee at ko-fi.com' /> <br/>
  
### Also check out [DarkRP Landing Page](https://github.com/1day2die/DarkRPLandingPage)
![image](https://user-images.githubusercontent.com/8725848/181248174-c2ca0b3a-65e5-4ef4-b7b6-eee48318e8a2.png)

ULX-Global-Ban
==============
A Global Ban System designed to be used only with ULX & ULib Garry's Mod servers.  
This Version has been fixed up by 1Day2Die#1337 (feel free to contact me on discord https://discord.gg/UgJcvgCg6N or via DM)

INFO
==============
* Created out of necessity.
* Created by NigNog (Bcool) and Adobe Ninja.
* This Version has been fixed up by 1Day2Die

FEATURES
==============

Server Identification: When your server starts, it checks to see whether or not the IP:Port configuration is already there. If it's not it'll create it and assign the server an ID. If it is it'll assign the server an existing ID.

Bans: This addon overwrites the exists ULib function to add a ban, and inserts it into the MySQL Database.

Authentication: When the server Auths a player, the point where the SteamID is retrived and the normal ban status is checked, this addon checks the MySQL server to see if the user has been banned.

Information: When a user has been banned, their SteamID, Current Name, Reason, ServerID of the server which they where banned on, Admin(Banner) SteamID and Current admin name are all saved in the MySQL Database.
Each time your server is started, the hostname is automagicly updated in the MySQL database.

Web: 3 Themes to decide from

PLANED/TODO
==============
* Nothing

CHANGELOG
==============
2025
* [FIX] Temp bans not being removed

--
* [ADD]		Web - New Modern theme
* [ADD]		Web - Steam API
* [ADD]		Web - Layout Changes
* [ADD]		Web - Webinstaller
* [FIX] 	Made Operable again.
* [ADD] 	Ban removal.
* [ADD]		Ban Modification.
* [ADD] 	Ban listing in the xgui menus.
* [ADD]		Ban sync, your bans defaulty sync with the MySQL DB every 30 seconds.
* [ADD]		Config file with a small amount of configs for extra control.
* [ADD]		Limited GateKeeper functionality.
* [FIX]		Permanently Banned users could still get through.
* [CHG]		SQL Table Structure to incorporate all ULX ban details.

INSTALLATION
==============
*Requires MySQLOO v9
*Requires ULX and ULib
*Requires PHP Extention gmp (apt install phpx.x-gmp)

Create a new Database.

Upload the Webfiles to your webserver and open the website.
You should now see the Installer.
Run through the Installer and everything will be setup for you

Upload the ulx_globalbanmodule to your garrysmod/addons directory and edit "gb_config" to your liking.

Everything should be running smooth!



THEMES
==============
![image](https://user-images.githubusercontent.com/8725848/181248394-5c2fe591-b458-44d3-891a-3e3c276ee17a.png)

![image](https://user-images.githubusercontent.com/8725848/181248417-7dde5b36-28e9-4224-9ed2-4e1adeb6997f.png)


![image](https://user-images.githubusercontent.com/8725848/181248445-76eaef5b-6a75-496c-9d07-d0f4bd417a4b.png)


<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en_US"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en_US">Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License</a>.
