<?php

namespace Mini\Model;

use PDO;

class Model
{
    /**
     * The database connection
     * @var PDO
     */
	private $db;

    /**
     * When creating the model, the configs for database connection creation are needed
     * @param $config
     */
    function __construct($config)
    {
        // PDO db connection statement preparation
        $dsn = 'mysql:host=' . $config['db_host'] . ';dbname='    . $config['db_name'] . ';port=' . $config['db_port'];

        // note the PDO::FETCH_OBJ, returning object ($result->id) instead of array ($result["id"])
        // @see http://php.net/manual/de/pdo.construct.php
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

        // create new PDO db connection
        $this->db = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
	}

    public function getAmountOfSongs()
    {
        $sql = "SELECT COUNT(id) AS amount_of_songs FROM song";
        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetch()->amount_of_songs;
    }

    /**
     * Get all songs from database
     */
    public function getAllSongs()
    {
        $sql = "SELECT id, artist, track, link, year, country, genre FROM song";
        $query = $this->db->prepare($sql);
        $query->execute();
        // fetchAll() is the PDO method that gets all result rows, here in object-style because we defined this in
        // core/controller.php! If you prefer to get an associative array as the result, then do
        // $query->fetchAll(PDO::FETCH_ASSOC); or change core/controller.php's PDO options to
        // $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ...
        return $query->fetchAll();
    }

    /**
     * Add a song to database
     * Please note that it's not necessary to "clean" our input in any way. With PDO all input is escaped properly
     * automatically. We also don't use strip_tags() etc. here so we keep the input 100% original (so it's possible
     * to save HTML and JS to the database, which is a valid use case). Data will only be cleaned when putting it out
     * in the views (see the views for more info).
     * @param string $artist Artist
     * @param string $track Track
     * @param string $link Link
     * @param string $year Year
     * @param string $country Country
     * @param string $genre Genre
     */
    public function addSong($artist, $track, $link, $year, $country, $genre)
    {
        $sql = "INSERT INTO song (artist, track, link, year, country, genre) VALUES (:artist, :track, :link, :year, :country, :genre)";
        $query = $this->db->prepare($sql);
        $parameters = array(':artist' => $artist, ':track' => $track, ':link' => $link, ':year' => $year, ':country' => $country, ':genre' => $genre);
        // useful for debugging: you can see the SQL behind above construction by using:
        // echo '[ PDO DEBUG ]: ' . \PdoDebugger::show($sql, $parameters); exit();
        $query->execute($parameters);
    }

    /**
     * Delete a song in the database
     * Please note: this is just an example! In a real application you would not simply let everybody
     * add/update/delete stuff!
     * @param int $song_id Id of song
     */
    public function deleteSong($song_id)
    {
        $sql = "DELETE FROM song WHERE id = :song_id";
        $query = $this->db->prepare($sql);
        $parameters = array(':song_id' => $song_id);
        // useful for debugging: you can see the SQL behind above construction by using:
        // echo '[ PDO DEBUG ]: ' . \PdoDebugger::show($sql, $parameters); exit();
        $query->execute($parameters);
    }

    /**
     * Get a song from database
     * @param int $song_id Id of song
     * @return mixed
     */
    public function getSong($song_id)
    {
        $sql = "SELECT id, artist, track, link, year, country, genre FROM song WHERE id = :song_id LIMIT 1";
        $query = $this->db->prepare($sql);
        $parameters = array(':song_id' => $song_id);
        // useful for debugging: you can see the SQL behind above construction by using:
        // echo '[ PDO DEBUG ]: ' . \PdoDebugger::show($sql, $parameters); exit();
        $query->execute($parameters);
        // fetch() is the PDO method that get exactly one result
        return $query->fetch();
    }

    /**
     * Update a song in database
     * Please note that it's not necessary to "clean" our input in any way. With PDO all input is escaped properly
     * automatically. We also don't use strip_tags() etc. here so we keep the input 100% original (so it's possible
     * to save HTML and JS to the database, which is a valid use case). Data will only be cleaned when putting it out
     * in the views (see the views for more info).
     * @param int $song_id Id
     * @param string $artist Artist
     * @param string $track Track
     * @param string $link Link
     * @param string $year Year
     * @param string $country Country
     * @param string $genre Genre
     */
    public function updateSong($song_id, $artist, $track, $link, $year, $country, $genre)
    {
        $sql = "UPDATE song SET artist = :artist, track = :track, link = :link, year = :year, country = :country, genre = :genre WHERE id = :song_id";
        $query = $this->db->prepare($sql);
        $parameters = array(':artist' => $artist, ':track' => $track, ':link' => $link, ':year' => $year, ':country' => $country, ':genre' => $genre, ':song_id' => $song_id);
        // useful for debugging: you can see the SQL behind above construction by using:
        // echo '[ PDO DEBUG ]: ' . \PdoDebugger::show($sql, $parameters); exit();
        $query->execute($parameters);
    }

    /**
     * Search
     * A super-simple search via LIKE. In a real world scenario you would use MATCH AGAINST and column indexes.
     * @param $search_term
     * @return array
     */
    public function searchSong($search_term)
    {
        $sql = "SELECT id, artist, track, link, year, country, genre FROM song WHERE (artist LIKE :search_term) OR (track LIKE :search_term);";
        $query = $this->db->prepare($sql);
        $parameters = array(':search_term' => '%' . $search_term . '%');
        // useful for debugging: you can see the SQL behind above construction by using:
        // echo '[ PDO DEBUG ]: ' . \PdoDebugger::show($sql, $parameters); exit();
        $query->execute($parameters);

        return $query->fetchAll();
    }
}
