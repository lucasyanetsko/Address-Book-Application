<?php

// Lucas Yanetsko IT 4400 Midterm Project
// Purpose: This file contains the PHP code to display the address book entries in a table format
// and allow the user to delete entries from the address book.
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

/// This function validates the name input from the user
function validateName($data, $fieldName)
{
  global $errorCount;
  if (empty($data)) {
    echo "\"$fieldName\" is a required field. <br />\n";
    ++$errorCount;
    $retval = "";
  } else {

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

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="author" content="Lucas Yanetsko">
  <title>View Address Book</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }

    th,
    td {
      border: 5px solid black;
      padding: 8px;
      text-align: left;
    }

    th {
      cursor: pointer;
    }

    .delete-btn {
      background-color: #023E8A;
      color: white;
      border: none;
      padding: 10px 14px;
      cursor: pointer;
    }

    .delete-btn:hover {
      background-color: #00B4D8;
    }
  </style>
</head>

<body>

  <h2>Address Book Entries</h2>

  <?php if ($entryAdded && $errorCount == 0 && $_SERVER["REQUEST_METHOD"] == "POST") : ?>
    <p>Your entry has been added. <a href="DisplayAddressBook.php">Click Here to view the Address Book</a>.</p>
  <?php endif; ?>

  <!-- Table that contains Address Book entries -->
  <table>
    <tr>
      <th onclick="sortTable(0)">Name</th>
      <th onclick="sortTable(1)">Email</th>
      <th onclick="sortTable(2)">Phone Number</th>
      <th>Delete</th>
    </tr>
    <?php
    $addressBookContent = file("AddressBook.txt", FILE_IGNORE_NEW_LINES);
    if ($addressBookContent === false) {
      echo "<tr><td colspan='4'>Error reading address book.</td></tr>\n";
    } else {
      foreach ($addressBookContent as $entry) {
        list($name, $email, $phone) = explode("\t", $entry);
        echo "<tr>";
        echo "<td>$name</td>";
        echo "<td>$email</td>";
        echo "<td>$phone</td>";
        echo "<td><button class='delete-btn' onclick='deleteEntry(this)'>Delete</button></td>";
        echo "</tr>\n";
      }
    }
    ?>
  </table>

  <!-- This is the JavaScript code for table sorting by
  selecting on the column header as well as deletion -->
  <script>
    function sortTable(n) {
      var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
      table = document.querySelector("table");
      switching = true;
      dir = "asc";
      while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
          shouldSwitch = false;
          x = rows[i].getElementsByTagName("td")[n];
          y = rows[i + 1].getElementsByTagName("td")[n];
          if (dir == "asc") {
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
              shouldSwitch = true;
              break;
            }
          } else if (dir == "desc") {
            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
              shouldSwitch = true;
              break;
            }
          }
        }
        if (shouldSwitch) {
          rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
          switching = true;
          switchcount++;
        } else {
          if (switchcount == 0 && dir == "asc") {
            dir = "desc";
            switching = true;
          }
        }
      }
    }
    // This function deletes the entry from the table with the click of a button 
    // and sends an AJAX request to delete the entry from the server-side.
    function deleteEntry(button) {
      var row = button.parentNode.parentNode;
      var rowIndex = row.rowIndex;
      if (confirm("Are you sure you want to delete this entry?")) {
        row.parentNode.removeChild(row);
        // Sends AJAX request to delete entry from server-side

      }
    }
  </script>

  <!-- Link to go back to the main form page and add more Address Book entries. -->
  <p><a href="AddressBook.html">Go Back to Add More Entries</a></p>

</body>

</html>