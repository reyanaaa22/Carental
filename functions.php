<?php
require_once('db.php');

/**
 * Log user activity to the users_activity_log table
 * 
 * @param PDO $dbh Database connection
 * @param int $user_id The ID of the user performing the action
 * @param string $activity Description of the activity
 * @param array $details Optional additional details about the activity
 * @return bool True if logging was successful, false otherwise
 */
function logUserActivity($dbh, $user_id, $activity, $details = []) {
    try {
        error_log("Attempting to log user activity - User ID: $user_id, Activity: $activity");
        
        // Make sure we're using PDO
        if (!($dbh instanceof PDO)) {
            error_log("Connection is not PDO, creating new connection");
            // If not PDO, create a new PDO connection
            $dbh = new PDO("mysql:host=localhost;dbname=ocrms", "root", "");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        // Format activity message based on type and details
        $activityMessage = formatActivityMessage($activity, $details);

        $stmt = $dbh->prepare("INSERT INTO users_activity_log (user_id, activity) VALUES (:user_id, :activity)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':activity', $activityMessage, PDO::PARAM_STR);
        
        $success = $stmt->execute();
        
        if ($success) {
            error_log("Successfully logged user activity");
        } else {
            error_log("Failed to log user activity. Error: " . implode(", ", $stmt->errorInfo()));
        }
        
        return $success;
    } catch (PDOException $e) {
        error_log("Failed to log user activity: " . $e->getMessage());
        return false;
    }
}

/**
 * Format activity message based on type and details
 * 
 * @param string $activity The type of activity
 * @param array $details Additional details about the activity
 * @return string Formatted activity message
 */
function formatActivityMessage($activity, $details = []) {
    switch ($activity) {
        case 'profile_update':
            $fields = isset($details['fields']) ? implode(', ', $details['fields']) : 'information';
            return "Updated profile $fields";
            
        case 'password_change':
            return "Changed account password";
            
        case 'car_booking':
            $carInfo = isset($details['car']) ? $details['car'] : 'a vehicle';
            $dates = '';
            if (isset($details['start_date']) && isset($details['end_date'])) {
                $dates = " from " . date('M d, Y', strtotime($details['start_date'])) . 
                        " to " . date('M d, Y', strtotime($details['end_date']));
            }
            return "Booked $carInfo$dates";
            
        case 'booking_cancel':
            $bookingId = isset($details['booking_id']) ? "#{$details['booking_id']}" : '';
            return "Cancelled booking$bookingId";
            
        case 'booking_modify':
            $bookingId = isset($details['booking_id']) ? "#{$details['booking_id']}" : '';
            return "Modified booking$bookingId";
            
        case 'review_submit':
            $carInfo = isset($details['car']) ? $details['car'] : 'a vehicle';
            return "Submitted review for $carInfo";
            
        case 'login':
            return "Logged in successfully";
            
        case 'logout':
            return "Logged out";
            
        case 'register':
            return "Created new account";
            
        case 'email_verify':
            return "Verified email address";
            
        default:
            return $activity;
    }
}

/**
 * Get user activity log for a specific user
 * 
 * @param PDO $dbh Database connection
 * @param int $user_id The ID of the user
 * @param int $limit Optional limit for number of records to return
 * @return array Array of activity log entries
 */
function getUserActivityLog($dbh, $user_id, $limit = 20) {
    try {
        error_log("Attempting to get user activity log - User ID: $user_id, Limit: $limit");
        
        // Make sure we're using PDO
        if (!($dbh instanceof PDO)) {
            error_log("Connection is not PDO, creating new connection");
            // If not PDO, create a new PDO connection
            $dbh = new PDO("mysql:host=localhost;dbname=ocrms", "root", "");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        $sql = "SELECT activity, log_time 
                FROM users_activity_log 
                WHERE user_id = :user_id 
                ORDER BY log_time DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        if ($limit > 0) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $success = $stmt->execute();
        
        if ($success) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Successfully retrieved " . count($results) . " activity log entries");
            return $results;
        } else {
            error_log("Failed to get user activity log. Error: " . implode(", ", $stmt->errorInfo()));
            return [];
        }
    } catch (PDOException $e) {
        error_log("Failed to get user activity log: " . $e->getMessage());
        return [];
    }
}
?> 