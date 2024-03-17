<?php

// Lucas Yanetsko IT 4400 Midterm Project
// Purpose: This file contains the PHP code to validate the input from the Add Address to Book form and add the entry to the address book file.
$errorCount = 0;
$entryAdded = false;

// This function validates the email input from the user
function validateEmail($data, $fieldName)
{
  global $errorCount;
  if (empty($data)) {
    echo "\"$fieldName\" is a required field. <br />\n";
    ++$errorCount;
    $retval = "";
  } else {
    // This cleans up the input and removes any slashes
    $retval = trim($data);
    $retval = stripslashes($retval);
    // This pattern validates the email format
    $pattern = "/^[\w-]+(\.[\w-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/";
    if (!preg_match($pattern, $retval)) {
      echo "\"$fieldName\" is not a valid email address. <br />\n";
      ++$errorCount;
      $retval = "";
    }
  }
  return $retval;
}

// This function validates the phone number input from the user
function validatePhoneNumber($data, $fieldName)
{
  global $errorCount;
  if (empty($data)) {
    echo "\"$fieldName\" is a required field. <br />\n";
    ++$errorCount;
    $retval = "";
  } else {
    // This removes any non-numeric characters
    $retval = preg_replace("/[^0-9]/", "", $data);
    if (strlen($retval) !== 10) {
      echo "\"$fieldName\" must contain exactly 10 digits. <br />\n";
      ++$errorCount;
      $retval = "";
    }
  }
  return $retval;
}

// This function validates the name input from the user
function validateName($data, $fieldName)
{
  global $errorCount;
  if (empty($data)) {
    echo "\"$fieldName\" is a required field. <br />\n";
    ++$errorCount;
    $retval = "";
  } else {
    // This if statement checks to make sure the name only contains alphabetic characters
    if (!ctype_alpha(str_replace(' ', '', $data))) {
      echo "\"$fieldName\" can only contain alphabetic characters. <br />\n";
      ++$errorCount;
      $retval = "";
    } else {
      $retval = trim($data);
      $retval = stripslashes($retval);
    }
  }
  return $retval;
}

// This function checks to see if the entry is a duplicate
function isDuplicateEntry($name, $email, $phone)
{
  $entry = "$name\t$email\t$phone";
  $addressBookContent = file("AddressBook.txt", FILE_IGNORE_NEW_LINES);
  if ($addressBookContent !== false) {
    foreach ($addressBookContent as $existingEntry) {
      if ($existingEntry === $entry) {
        return true; // This entry already exists
      }
    }
  }
  return false; // This entry does not exist
}

// This is the function that adds an entry to the address book
function addEntryToAddressBook($name, $email, $phone)
{
  if (isDuplicateEntry($name, $email, $phone)) {
    echo "<p>A user with this information already exists in the address book. <a href=\"DisplayAddressBook.php\">Click Here to view the current address book</a>.</p>";
  } else {
    // This states that if the entry doesn't already exist, add it to the file
    $entry = "$name\t$email\t$phone";
    file_put_contents("AddressBook.txt", $entry . PHP_EOL, FILE_APPEND);
    $entryAdded = true;
  }
}

// This checks to see if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = validateName($_POST["name"], "Name");
  $email = validateEmail($_POST["email"], "Email");
  $phone = validatePhoneNumber($_POST["phone"], "Phone");

  if ($errorCount == 0) {
    addEntryToAddressBook($name, $email, $phone);
  }
}
?>

<!-- This is the message with a successful entry and link to view the Address Book. -->
<?php
if ($entryAdded && $errorCount == 0 && $_SERVER["REQUEST_METHOD"] == "POST") {
  echo "<p>Your entry has been added. <a href=\"DisplayAddressBook.php\">Click Here to view the Address Book</a>.</p>";
}
?>

<!-- Error message when entry wasn't added and like to return to form. -->
<?php
if ($errorCount > 0 || $_SERVER["REQUEST_METHOD"] != "POST") {
  echo "<p>Error adding entry to the address book. Return to the <a href=\"AddressBook.html\">Add Address to Book</a> page to try again!</p>";
}
?>