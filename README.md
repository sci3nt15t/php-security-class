# php-security-class

So this 100 lines of code is consist of 4 main simple functions!
Here is the help from me if you consider about using this class to increase your websites security!


1.For using the class at first you should pass 2 arguemtns to it!
security(bool start_session_check,bool check_brute)

these two boolean arguements will check either you want to sart the sessions and check for brute force attacks!
if you dont allow sessions to be started the secure_session function can not be used!

if the check_brute is not allowed this class will not prevent bruteforces! the brute function will just record
the attempts and ip's!

WARNING!: my code will ban the users with greater number of attempts than it is known in the code permanantly! 
and i have put maximum attempts at 3! consider change it to satisfy your needs!or you have to delete records manualy!


2.secure_session: this function will bring more secure session than it is!i didnt made it complexe so users wont
face any problem by changing it! only arguement needed is session name!

3.SAFE_GET: most developers have trouble prevent sql injection!!! it is so simple! so i come with this function! use it just like $_GET function!
WARNING!: the following function will only support integer values! char and string are not supported yet!

4.brute and debrute function:
brute force attacks are one of the most dangrious attacks yet! so prevent will help you web application security a lot! i have made a simple algorithm to prevent brute force attacks! unfortuenately changing ip addresses will bypass this function! but with small amount of attempts(like 3) hacker should use thousands of ips in order to have a successfull attack! anyway this is the code that i come up with!

usage is simple! if the login is failed use brute function! and when the login is successfull use debrute function to delete records have made by brute function!


following code will give you better understanding of usages!


<?php
include("security.php");


$sec = new security(true,true);


$sec->secure_session("sci3nt15t");

echo $sec->SAFE_GET("id");


if(login() == fail)

{

	$sec->brute();
	
}

elseif (login() == successfull)

{

	$sec->debrute();	
	
}

?>
