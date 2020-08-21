# Calendar
This is my first laravel project that will go live. The views are basic, the most important thing was setting a project to use the generators. 

This is created for my Thursday evening tennis group. Each season someone needed to create a season with 10 players making sure everyone played with everyone divided over 3 teams. Until recent this was done in Excel which took a long time and was complex to do.

With this project the calendar can be viewed online. The season is created in a matter of seconds and meets all the expectations of the players.
conditions:
 * - the time between play dates shouldn't be more then 2 weeks(with the exception if players are absence)
 * - Everyone should play at least once with everyone in team 1 or 2(single match)
 * - you shouldn't be in the same team with a player you have played before in the same team.
 * exception:
 * - if the season is really long then it is possible to play more then once in the same team
 * - If there are a lot of absence people on the same day it is possible to play more then once in the same team
 
 For now there is only one generator in it. It will create the season with the conditions described. 

Adding a new generator shouldn't be to hard. All that needs to be done is create the generator. Add the view in the generatorpartials folder and add the option to the type in the creating season folder.

# Version 1: 
After one season the backend was rewritten. Some of the controllers did to much so we split up some controllers so the controllers are mostly resource controllers now. We added socialite login options(Google, Microsoft, Facebook) to make the login easier. 

The generator working has also been reworked. The abstract class has been removed and an interface has been created. 
All generators will need the following methods
    - generateSeason: method to generate the season
    - getPlayDates: gets all the days in a season
    - saveSeason: saves the season
    - getNextPlayDay: gets the next play day
    - getSeasonCalendar: shows an existing season

To add a new Generator you need to do 3 things. Create a new generator with the above methods, create a view and add the options to the dropdowns of a season. 

# Version 2.00: 
Some major changes: 
The controllers has been changed to REST API. The front is changed from blade to a Vue.js front-end with the use of vue router and vue store. 

This version isn't completly finished. Next things are still in development: 
- main page calendar
- tweaking a season calendar
- generating a season.

# Version 2.01: 
- added generator
- added main page calendars

# Future
finish version 2
Add more generators
- singe field generator
