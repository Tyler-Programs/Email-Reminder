<?php 
class Reminder {
    private $uid; // database uid for the event
    private $start_date;
    private $start_time;
    private $end_date;
    private $end_time;
    private $location;
    private $title;
    private $notes;
    private $remind_before_mins; // the number of minutes before the starting date+time to send the reminder.
    // Null or 0 will send the email reminder at the start date+time.
    private $email;
    // 
    public function __construct($row = null) {
        if ($row != null) {
           $this->uid = $row['UID'];
           $this->start_date = substr($row['start_date_time_utc'], 0, 10);
           $this->start_time = substr($row['start_date_time_utc'], 11, 8);
           $this->end_date = substr($row['end_date_time_utc'], 0, 10);
           $this->end_time = substr($row['end_date_time_utc'], 11, 8);
           $this->location = $row['location'];
           $this->title = $row['title'];
           $this->notes = $row['notes'];
           $this->email = $row['email'];
        }
        return $this;
    }

    public function toJSON() {
        $remindtime = 'stuff'; // do calculation to subtract $remind_before_mins from $start_time. 
        return json_encode([
            'start_date_time' => $this->start_date . " " . $this->start_time,
            'end_date_time' => $this->end_date . " " . $this->end_time,
            'send_time' => $remindtime,
            'title' => $this->title,
            'notes' => $this->notes,
            'email' => $this->email,
            'location' => $this->location
        ]);
    }

    // ------------- Getters & Setters --------------------
    public function get_start_date() {
        return $this->start_date;
    }

    public function set_start_date($start_date) {
        $this->start_date = $start_date;
    }

    public function get_start_time() {
        return $this->start_time;
    }

    public function set_start_time($start_time) {
        $this->start_time = $start_time;
    }

    public function get_end_date() {
        return $this->end_date;
    }

    public function set_end_date($end_date) {
        $this->end_date = $end_date;
    }

    public function get_end_time() {
        return $this->end_time;
    }

    public function set_end_time($end_time) {
        $this->end_time = $end_time;
    }

    public function get_location() {
        return $this->location;
    }

    public function set_location($location) {
        $this->location = $location;
    }

    public function get_title() {
        return $this->title;
    }

    public function set_title($title) {
        $this->title = $title;
    }

    public function get_notes() {
        return $this->notes;
    }

    public function set_notes($notes) {
        $this->notes = $notes;
    }

    public function get_remindBeforeMins() {
        return $this->remindBeforeMins;
    }

    public function set_remindBeforeMins($remind_before_mins) {
        $this->remindBeforeMins = $remind_before_mins;
    }

    public function get_email() {
        return $this->email;
    }

    public function set_email($email) {
        $this->email = $email;
    }

    public function get_uid() {
        return $this->uid;
    }
    // ------------- Getters & Setters --------------------
}
?>