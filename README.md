# justbooking
Created a basic booking website using HTML, PHP and SQL to book IT training sessions offered by a university for their students 
A university offers a range of IT training sessions during welcome week that students can book in advance. The sessions cover topics such as Word Processing, Spreadsheets, etc. To keep the system simple, we assume that the students do not have computer accounts yet but instead enter a few personal details when they book a training session. Sessions on a particular topic are typically offered more than once during welcome week. Every time a session on a particular topic runs it can only accommodate a certain number of participants; in the following we will call this the capacity of the session. We keep the capacity of sessions small. Initially, the number of places available on a specific session is equal to its capacity.
The web-based application should allow a student to

select a topic via a drop-down menu;
select a day of the week and time at which a session on that topic is offered via a separate drop-down menu;
enter their name via a text field;
enter their e-mail address via another text field (in case the student needs to be contacted);
after selecting/entering the data above, submit a booking request by pressing a `Submit' button.
Ideally, a student is able to enter all this data via a single web page (not a sequence of two or more pages). However, a sequence of web pages can be used if this is the only way that you are able to realise this application.

The menus should be populated with data from a database.

On submission of a booking request, a student should be shown a confirmation whether the booking request has been successful or unsuccessful. In addition to an indication whether the booking request has been successful or not, the application should output a table with all the bookings. The table should include the topic, day of the week and time, name and e-mail address of each booking. This is subject to the following conditions:

The application should ensure that the string entered as a name satisfies the following constraints: A name only consist of letters (a-z and A-Z), hyphens, apostrophes and spaces; contains no sequence of two or more of the characters hyphen and apostrophe; starts with a letter or an apostrophe; does not end with a hyphen or a space. If these constraints are satisfied, then we call the name valid. If these constraints are not satisfied, then the application should display an error message and the booking request must be unsuccessful. This must be realised using PHP (not HTML5 nor JavaScript).
The application should ensure that the string entered by the user as an e-mail address has exactly one occurrence of @ that is preceded and followed by a non-empty sequence of the characters a-z, dot, underscore, hyphen, where neither sequence ends in a dot or a hyphen. An e-mail address that satisfies these constraints is called valid and the check that the user has entered a valid e-mail address must be performed using PHP only. If the input of the user is not a valid e-mail address, then an error message should be shown and the booking request is unsuccessful.
If name and e-mail address are valid, then a booking request must be successful if the selected session still has at least one place left. On success, the number of places on the selected session is reduced by one and a record of the booking will be kept in the database, including the topic, time, name, and e-mail address.
A booking request must be unsuccessful if there are no places left on the selected session.
