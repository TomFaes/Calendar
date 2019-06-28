# Calendar
This is my first laravel project that will go live. The views are basic, the most important thing was setting a project to use the generators. views may change over time

This is created for my Thursday evening tennis group. Each season someone needed to create a season with 10 players making sure everyone played with everyone divided over 3 teams. Until recent this was done in Excel which took long and was complex and hard to do.

With this project the calendar will be online. The season is created in a matter of seconds and meets all the expectations of the players.
conditions:
 * - the time between play dates shouldn't be more the 2 weeks(with the exception if players are absence)
 * - Everyone should play at least once with everyone in team 1 or 2
 * - you shouldn't be in the same team with a player you have played before in the same team.
 * exception:
 * - if the season is really long then it is possible to play more then once in the same team
 * - If there are a lot of absence people on the same day it is possible to play more then once in the same team
 
 For now there is only one generator in it. It will create the season with the conditions described. 

Adding a new generator shouldn't be to hard. All that needs to be done is create the generator. Add the view in the generatorpartials folder and add the option to the type in the creating season folder.