<?php
/**
 * custom ORM ( Object Relationnal Mapping )
 * Build and propulsed by @Hisoka 
 * Find on github
 */

class DB
{
    public PDO $db;
    static $host     = "your_host";
    static $dbname   = "your_db_name";
    static $username = "your_username";
    static $password = "your_password";
    static $port           = "your_port";
    protected $table;
    protected $query;
    protected $prepare;
    protected $limit = 0;
    protected $status;
    protected $error;

    /**
     * INSTANCE CONSTRUCTOR
     */

    public function __construct()
    {

        $this->db = new PDO('mysql:host=' . self::$host . ';dbname=' . self::$dbname . ';charset=utf8;port=35348', self::$username, self::$password, [
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]);

        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * TABLE REQUEST
     */

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * INSERT REQUEST
     */

    public function insert(array $data): self
    {
        $query = "INSERT INTO $this->table SET ";

        foreach ($data as $key => $item) :

            $query .= " $key = '$item' ,";

        endforeach;

        $this->query = substr($query, 0, -1);

        return $this;
    }

    /**
     * UPDATE REQUEST
     */

    public function update(array $data): self
    {
        $query = "UPDATE $this->table SET ";

        foreach ($data as $key => $item) :

            $query .= " $key = '$item' ,";

        endforeach;

        $query = substr($query, 0, -1);

        $this->query = $query;

        return $this;
    }

    /**
     * SELECT REQUEST
     */

    public function select(array  $data): self
    {
        $query = "";

        if (is_array($data)) {

            if (count($data) == 0) {

                $query = "SELECT * FROM  $this->table ";
            } else {

                $query = "SELECT ";

                foreach ($data as $key => $item) :

                    $query .= " $item ,";

                endforeach;

                $query = substr($query, 0, -1); // remove <,>

                $query .= " FROM  $this->table ";
            }

            $this->query = $query;
        }

        return $this;
    }

    /**
     * DELETE REQUEST
     */

    public function delete(): self
    {
        $query = "DELETE FROM $this->table ";

        $this->query = $query;

        return $this;
    }

    /**
     *  LINIT STATEMEMENT
     */

    public function limit(int $limit = 1): self
    {

        $this->limit = $limit;

        if ($this->limit != 0)  $this->query .= " LIMIT " . $this->limit;

        return $this;
    }


    /**
     *  WHERE STATEMEMENT
     */
    public function where(array | int $data): self
    {
        $query = "";

        if (is_int($data)) {

            $query = " WHERE 1 ";
        } else if (is_array($data)) {

            $query = " WHERE ";

            foreach ($data as $key => $item) :

                if (!empty($item)) $query .= " $key = '$item' AND";

            endforeach;

            if ($query == " WHERE ") {
                $query .= " 1";
            } else {
                $query = substr($query, 0, -1); // remove <D>

                $query = substr($query, 0, -1); // remove <N>

                $query = substr($query, 0, -1); // remove <A>
            }
        } else {

            $err_type = gettype($data);

            die($err_type . ' : Type non pris en charge');
        }

        $this->query .= $query;

        return $this;
    }

    /**
     *  OR WHERE STATEMEMENT
     */
    public function orWhere(array | int $data): self
    {
        $query = "";

        if (is_int($data)) {

            $query = " WHERE 1 ";
        } else if (is_array($data)) {

            $query = " WHERE ";

            foreach ($data as $key => $item) :

                if (!empty($item)) $query .= " $key = '$item' OR";

            endforeach;

            $query = substr($query, 0, -1); // remove <O>

            $query = substr($query, 0, -1); // remove <R>

        } else {

            $err_type = gettype($data);

            die($err_type . ' : Type non pris en charge');
        }

        $this->query .= $query;

        return $this;
    }

    /**
     *  GENERATE STATEMEMENT
     */

    public function generateSQL()
    {

        return $this->query;
    }

    /**
     *  EXECUTE STATEMEMENT
     */

    public function execute(): self
    {

        if (empty($this->table)) {
            die('Fichier de donnee manquant');
        }

        try {

            $prepare = $this->db->prepare($this->query);

            $this->status = $prepare->execute();

            $this->prepare = $prepare;

            return $this;
        } catch (PDOException $e) {

            $this->error = [
                "code" => "error",
                "message" => $e->getMessage()
            ];

            return  $this;
        }
    }

    /**
     *  ASSOCIATIVE RENDER STATEMEMENT
     */

    public function fetchAssociative(): array
    {

        $fetch = $this->prepare->fetchAll(PDO::FETCH_ASSOC);

        return $fetch;
    }

    /**
     *  OBJECT RENDER STATEMEMENT
     */

    public function fetchObject(): object
    {

        $fetch = $this->prepare->fetchAll(PDO::FETCH_OBJ);

        return $fetch;
    }

    /**
     *  STATUS STATEMEMENT
     */

    public function status()
    {
        return $this->status;
    }

    /**
     *  ERROR STATEMEMENT
     */

    public function debug(): array
    {
        return $this->error;
    }
}


$DB =  new DB();
