<?php 
class User {
    private $id; // database uid for the user
    private $email; // user's email to send reminders to
    private $first_name;
    private $last_name;
    private $locale; // users locale settings (to automatically handle timezones)

    // ------------- Getters & Setters --------------------
    public function get_email() {
        return $this->email;
    }

    public function set_email($email) {
        $this->email = $email;
    }

    public function get_first_name() {
        return $this->first_name;
    }

    public function set_first_name($first_name) {
        $this->first_name = $first_name;
    }

    public function get_last_name() {
        return $this->last_name;
    }

    public function set_last_name($last_name) {
        $this->last_name = $last_name;
    }

    public function get_locale() {
        return $this->locale;
    }

    public function set_locale($locale) {
        $this->locale = $locale;
    }

    public function get_id() {
        return $this->id;
    }
    // ------------- Getters & Setters --------------------
}
?>