##Author: Simon Yousoufov

##Notes:
+ Written as a pseudo-API. Each question is the code test is contained to its own controller under app/controllers.
+ I did not push to a remote repo until I was mostly done writing code, so my branch structure isn't reflected.
+ I opted out of setting up pretty urls just for a quicker setup time.

##Setup:
+ This is written with the Laravel framework. The repository was uploaded with all dependencies so a composer isntall should not be necessary.
+ Include your hostname in bootstrap/start.php under 'Simon-Notebook' (http://laravel.com/docs/4.2/installation#configuration for reference)
+ Ensure that the folder structure is set to 755 (chmod 755 -R codetest) and app/storage is writeable (chmod -R o+w codetest/app/storage)

##Sending requests:
#####Using DHC, a similar REST client, or just navigating to the appropritate URL + parameters will do it.
######Routes are located in app/routes.php
+ Question one:
  + {codetestserverroot}/public/index.php/description?query={R7A,R8A,C4-4A,M3-2,R8B,C1-6A,R7B,R8X,C1-7A,PARK,C1-9A,R6,C1-7,C2-6,R10,C4-5,C6-3X,C1-6,C6-2M,C6-4M,M2-4,M1-5/R7X} will return the test on the array of codes provided in the question. It will also accept any arbitrary codes.
  + {codetestserverroot}/public/index.php/descriptors will return the list of descriptors to show how I organized the mock database
  + The mock database is located in codetest/app/storage/mockdb/codes.json
+ Question two:
  + To send a request that will return the results of the array given in the question:
    + {codetestserverroot}/public/index.php/consecutive?random=false&query={3,4,1,1,6,11,15,6,7,8,10,9,8,2,3}
    + To request a random test of an array of 50 numbers 1-19, just send random=true
    + NOTE: The response is in the form of array(original index in array of numbers => the number value)
+ Question three:
  + To send a request for the default set of topics (Top Stories, Latest, Entertainment): {codetestserverroot}/public/index.php/news?customSearch=false
  + To send a custom query of topics: {codetestserverroot}/public/index.php/news?customSearch=true&topics={world,us,crime,tech}
  + Possible topics to choose from: topstories, world, us, latest, allpolitics, crime, tech, health, showbiz, travel, living, freevideo, studentnews, mostpopular





##The original question prompts
Question 1
Description:
Write a method which accepts a list/array of codes and returns the code along with its description as JSON.  Note that the range for R1-1 - R10H encompasses R1-1 to R10-10 and R1A to R10H.

Bonus:
Write a unit test.

Description Data:
Codes				Descriptions
---------------------------------------------------------------
R1-1 - R10H			General Residence Districts
C1-6 - C8-4			Commercial Districts
M1-1 - M3-2			Manufacturing Districts
M1-1/R5 – M1-6/R10	Mixed Manufacturing & Residential Districts
BPC					Battery Park City
PARK				New York City Parks
PARKNYS				New York State Parks
PARKUS				United States Parks
ZNA					Zoning Not Applicable
ZR 11-151			Special Zoning District

Example output:
Given ['M3', 'R3-2', 'PARKNYS', 'M1-3/R9']
```json
{
	"codes":[
		{"code":"M3", "description”:”Not found“},
		{"code":"R3-2", "description":"Residential Districts"},
		{"code":"PARKNYS", "description":"New York State Parks"},
		{"code":"M1-3/R9", "description":"Mixed Manufacturing & Residential Districts"}
	]
}
```

Input:
The list is:
	R7A
	R8A
	C4-4A
	M3-2
	R8B
	C1-6A
	R7B
	R8X
	C1-7A
	PARK
	C1-9A
	R6
	C1-7
	C2-6
	R10
	C4-5
	C6-3X
	C1-6
	C6-2M
	C6-4M
	M2-4
	M1-5/R7X

---------------------------------------------------------------
---------------------------------------------------------------
---------------------------------------------------------------

Question 2
Given an array of unsorted positive integers, write a function that finds runs of consecutive numbers (either ascending or descending) and returns the indices where such runs begin. If no such runs are found, return null.

Example: [1, 1, 3, 5, 6, 8, 10, 11, 10, 9, 8, 9, 10, 11, 7] would return [3,6,7,10]

Bonus: Come up with a reusable test plan for the function.
Bonus 2: Write it as a unit test.

---------------------------------------------------------------
---------------------------------------------------------------
---------------------------------------------------------------

Question 3
Build and document a php or python-based script which extracts story information from the homepage (only the homepage) of this site:
http://www.cnn.com/ 
The returned data should contain information on each story including its link, ALT text and categorize each article link under its respective slug ("Opinion", "Breaking News", etc).
Bonus: Get the meta-description of each underlying page.
