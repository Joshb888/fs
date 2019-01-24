<?php
    session_name('hydrid');
    session_start();
    require '../../../connect.php';
    require '../../../config.php';
    require '../../../backend/user/auth/userIsLoggedIn.php';

    // Makes sure the person actually has a character set
    if (!isset($_SESSION['identity_name'])) {
      header('Location: ../../../../' . $url['leo'] . '?v=nosession');
      exit();
    }

    // Page PHP
    $searchCharId = strip_tags($_GET['id']);
    $getChar = "SELECT * FROM characters WHERE character_id='$searchCharId'";
        $result  = $pdo->prepare($getChar);
        $result->execute();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $suspect_name = $row['first_name'] . ' ' . $row['last_name'];
            $charid       = $row['character_id'];
            echo '<div class="float-right">';
            echo '<div style="border: 1px solid black; overflow-y: scroll; width:500px; height:150px; margin-top:300px;">';
            echo "<center>PREVIOUS TICKETS</center>";
            echo "<table class='table table-borderless'>
                          <thead>
                            <tr>
                            <th>Ticket_ID</th>
                            <th>Reason</th>
                            <th>Postal</th>
                            <th>Timestamp</th>
                            </tr>
                          </thead>";
            $getPreviousTickets = "SELECT * FROM tickets WHERE suspect_id = '$charid'";
            $result             = $pdo->prepare($getPreviousTickets);
            $result->execute();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['ticket_id'] . "</td>";
                echo "<td>" . $row['reasons'] . "</td>";
                echo "<td>" . $row['postal'] . "</td>";
                echo "<td>" . $row['ticket_timestamp'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
            echo '</div><br>';
            echo '<div style="border: 1px solid black; overflow-y: scroll; width:500px; height:150px;">';
            echo "<center>PREVIOUS ARRESTS</center>";
            echo "<table class='table table-borderless'>
                          <thead>
                            <tr>
                            <th>Arrest ID</th>
                            <th>Charges</th>
                            <th>Arresting Officer</th>
                            <th>Timestamp</th>
                            </tr>
                          </thead>";
            $getPreviousTickets = "SELECT * FROM arrest_reports WHERE suspect_id = '$charid'";
            $result             = $pdo->prepare($getPreviousTickets);
            $result->execute();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['arrest_id'] . "</td>";
                echo "<td>" . $row['summary'] . "</td>";
                echo "<td>" . $row['arresting_officer'] . "</td>";
                echo "<td>" . $row['timestamp'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
            echo '</div>';
            echo '</div>';
        }

        $searchCharId       = strip_tags($_GET['id']);
        $getChar = "SELECT * FROM characters WHERE character_id='$searchCharId'";
        $result  = $pdo->prepare($getChar);
        $result->execute();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<h5>Name: " . $row['first_name'] . " " . $row['last_name'] . "</h5><br-leo-name-search>";
            echo "<h5>Date Of Birth: " . $row['date_of_birth'] . "</h5><br-leo-name-search>";
            echo "<h5>Sex: " . $row['sex'] . "</h5><br-leo-name-search>";
            echo "<h5>Address: " . $row['address'] . "</h5><br-leo-name-search>";
            echo "<h5>Height / Weight: " . $row['height'] . " / " . $row['weight'] . "</h5><br-leo-name-search>";
            echo "<h5>Eye Color / Hair Color: " . $row['eye_color'] . " / " . $row['hair_color'] . "</h5><br-leo-name-search>";
            echo "<hr>";
            echo '<h5>Drivers License: '; if ($row['license_driver'] === "Suspended") {
                echo '<font color="red"><strong>Suspended</strong></font>';
            } else {
                echo $row['license_driver'];
            }
            echo '</h5><br-leo-name-search>';
            echo '<h5>Firearms License: '; if ($row['license_firearm'] === "Suspended") {
                echo '<font color="red"><strong>Suspended</strong></font>';
            } else {
                echo $row['license_firearm'];
            }
            echo "</h5><br-leo-name-search>";
            if ($_SESSION['identity_supervisor'] === "Yes") {
                echo '<input type="button" class="btn btn-danger btn-sm" name="suspendDriversLicense" value="Suspend Drivers License" id='.$searchCharId.' onclick="suspendDriversLicense(this)">';
                echo '  <input type="button" class="btn btn-danger btn-sm" name="suspendFirearmsLicense" value="Suspend Firearms License" id='.$searchCharId.' onclick="suspendFirearmsLicense(this)">';
            }
            echo "<hr>";
            echo "<div class='float-left'";
            echo "<h5 style='color:black;'>WARRANTS</h5>";
            echo "<table>";
            $person        = $row['first_name'] . " " . $row['last_name'];
            $wanted_status = "WANTED";
            $getWanted        = "SELECT * FROM warrants WHERE wanted_person='$person' AND wanted_status='$wanted_status'";
            $result2        = $pdo->prepare($getWanted);
            $result2->execute();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td><center><font color='red'><b>" . $row['reason'] . "</b></font></center></td>";
                echo "<td><center><font color='red'><b>" . $row['issued_on'] . "</b></font></center></td>";
                if ($_SESSION['identity_supervisor'] === "Yes") {
                    echo '<td><a class="btn btn-danger btn-sm" href="#" data-title="Delete"><i class="fas fa-minus-circle"></i></a></td>';
                }

                echo "</tr>";
            }

            echo "</table>";
            echo "</div>";
        }
