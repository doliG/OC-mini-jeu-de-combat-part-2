<?php

class PersoManager
{
    private $_db;

    /**
     * $_db construction
     * @param PDO $db
     */
    public function __construct($db)
    {
        $this->setDb($db);
    }

    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }

    /**
     * Add Perso $perso in db
     * @param Perso $perso
     */
    public function add(Perso $perso)
    {
        $sql = 'INSERT INTO personnages (name, damage)
                VALUES (:name, :damage)';

        $query = $this->_db->prepare($sql);
        $query->bindValue(':name', $perso->getName(), PDO::PARAM_STR);
        $query->bindValue(':damage', $perso->getDamage(), PDO::PARAM_INT);

        $query->execute();
    }

    /**
     * [update description]
     * @param  Perso  $perso [description]
     * @return [type]        [description]
     */
    public function update(Perso $perso)
    {
        $sql = 'UPDATE personnages
                SET name = :name, damage = :damage
                WHERE id = :id';
        $query = $this->_db->prepare($sql);

        $query->bindValue(':name', $perso->getName(), PDO::PARAM_STR);
        $query->bindValue(':damage', $perso->getDamage(), PDO::PARAM_INT);
        $query->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $query->execute();
    }

    /**
     * Select Perso depending on data, and return it.
     * @param  int|string    $data   Could be Perso's name or Perso's id
     * @return Perso
     */
     public function find($data)
     {
         if (is_int($data)) {
             $data = (int) $data;
             $sql = 'SELECT * FROM personnages WHERE id = :data';
             $filter = PDO::PARAM_INT;
         } else {
             $data = (string) $data;
             $sql = 'SELECT * FROM personnages WHERE name = :data';
             $filter = PDO::PARAM_STR;
         }

         $query = $this->_db->prepare($sql);
         $query->bindParam(':data', $data, $filter);
         $query->execute();
         $result = $query->fetch(PDO::FETCH_ASSOC);

         return ($result) ? new Perso($result) : 0;
     }

    /**
     * Delete Perso in db which have id = $id
     * @param  int    $id
     */
    public function delete(int $id)
    {
        $id = (int) $id;

        $sql = 'DELETE FROM personnages WHERE id = :id';
        $query = $this->_db->prepare($sql);

        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
    }

    /**
     * Count `personnages` rows in db
     * @return int Number of row in `personnages` table.
     */
    public function count()
    {
        $sql = 'SELECT COUNT(*) as number_perso FROM personnages';
        $res = $this->_db->query($sql)->fetch(PDO::FETCH_ASSOC);
        return intval($res['number_perso']);
    }

    /**
     * Return array of all Perso in db except Perso which have $nom
     * @param  string $nom Name of perso to exlude.
     * @return array       Array containing all Perso except one.
     */
    public function getList($name)
    {
        $sql = 'SELECT * FROM personnages ORDER BY id ASC';
        $res = $this->_db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $persos = [];
        foreach ($res as $row) {
            if ($row['name'] != $name) {
                $perso = new Perso($row);
                $persos[] = $perso;
            }
        }

        return $persos;
    }

    /**
     * Search if a perso exist in db.
     * @param  int|string $data Can be perso's id OR perso's name
     * @return bool             Return true if existe, false otherwise
     */
    public function exist($data)
    {
        if (is_int($data)) {
            $sql = 'SELECT COUNT(*) AS count FROM personnages WHERE id = :id';
            $query = $this->_db->prepare($sql);
            $query->bindValue(':id', $data, PDO::PARAM_STR);
        } else {
            $sql = 'SELECT COUNT(*) AS count FROM personnages WHERE name = :name';
            $query = $this->_db->prepare($sql);
            $query->bindValue(':name', $data, PDO::PARAM_STR);
        }
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return intval($result['count']);
    }
}
