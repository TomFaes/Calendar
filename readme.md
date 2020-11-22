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
- reworked generator
- added main page calendars

# Version 2.02: Current live version
- rework the generator to get less calls(data is gatered in the generator)
- update the calendar view + nex play day view
- team is now connected to the group user instead of a user. This way a user doesn't need an account to be part of the group. 
- change the delete group user option. user is removed from group but group user record isn't soft deleted. This was needed to keep past calendars active

# Version 2.03: 
- add swipe to the next play day view
- creation of 2 global layout components(input & layout)
- convert route files to tulpe methods
- improve code readability(generator, ...)
- bugfixes
    - unverified user of deleted group was shown on index page.
    - bug with create random string for password

# Version 2.04: 
- make option to see the season when you don't have an account
- the season can be updated after a season has started by the season admin

# Version 2.05: 
- implement phpUnit testing
- bugfixes
    - all fields were diplayed 2 times on creating a season

# Future
Version 2.06
    - implement option for the admin to add season absences for other players
    - player update season
        - add option for a player to request replacement
Version 2.07
    - Add more generators
        - singe field one hour double generator
        - 2 fields double hour generator.

Version 3.XX: 
    - update to Laravel 8
    
