Movie List by Diego Alonso Alvarez Arcila

The web application consists in a SignUp and Login page, with input validations with regular expressions, 
and a page that looking for movies through an API.

First, create an account in the SignUp page, if everything is fine, when the button "Sign Up" is clicked,
a message is displayed right above the button, indicating that the account was created successfully. Then,
click in the Log In button for go to the Login page, enter username and the password, and if everything is fine,
you will be redirected to the movie search page.

In the SignUp and Login page, if the validations are not correct, an error message is displayed below the
correspondent input indicating what's wrong.

In the MovieList page, if the input 'Movie Name' is not empty and the button 'Save' is submited, a file
will be created with the response of the API, a message, right above the button, will tell you.
The input Title is required for search, Year is optional, check the checkboxes if you want the information
sorted by title or year, and select the option if you want an ascending or descending order.
When the button 'Search' is clicked, a table with the name, year and poster of the movie will be shown.

The button Log Out will close the session and you will be redirected to the login page.
