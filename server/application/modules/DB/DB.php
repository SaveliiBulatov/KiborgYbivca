<?php

class DB
{
    //сохраняет соединение с ДБ
    private $pdo;

    //вызов соединения с БД
    public function __construct()
    {

//        local
//        $host = '127.0.0.1';
//        $port = 3306;
//        $user = 'root';
//        $pass = '';
//        $db = 'cyborgs';

        $host = 'server187.hosting.reg.ru';
        $port = '3306';
        $user = 'u2333359_dargven';
        $pass = 'bAq-UKv-YCK-fxx';
        $db = 'u2333359_Cyborgs';

        $connect = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
        $this->pdo = new PDO($connect, $user, $pass);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    // выполнить запрос без возвращения данных
    private function execute($sql, $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        return $sth->execute($params);
    }

    // получение ОДНОЙ записи
    private function query($sql, $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(PDO::FETCH_OBJ);
    }

    // получение НЕСКОЛЬКИХ записей
    private function queryAll($sql, $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }


//    НЕПОВТОРИМЫЙ ОРИГИНАЛ

    public function getUserByLogin($login)
    {
        return $this->query("SELECT * FROM users WHERE login=?", [$login]);
    }

    public function getUserByToken($token)
    {
        return $this->query("SELECT * FROM users WHERE token=?", [$token]);
    }

    public function getUserById($id)
    {
        return $this->query("SELECT * FROM users WHERE id=?", [$id]);
    }

    public function updateToken($id, $token)
    {
        $this->execute("UPDATE users SET token=? WHERE id=?", [$token, $id]);
    }

    public function addUser($login, $hash, $name, $email)
    {
        $this->execute(
            "INSERT INTO users (login,password,name,email) VALUES (?, ?, ?, ?)",
            [$login, $hash, $name, $email]
        );
    }

    public function setPassword($id, $password)
    {
        $this->execute("UPDATE users SET password =? WHERE id = ?", [$password, $id]);
    }

    public function addPlayerToTeam($id, $teamId)
    {
        $this->execute("INSERT INTO userTeams (team_id, user_id) VALUES (?, ?)", [$teamId, $id]);
    }

    public function sendMessage($id, $message)
    {
        $this->execute('INSERT INTO messages (user_id, message, created) VALUES (?,?, now())', [$id, $message]);
    }

    public function getMessage()
    {
        return $this->queryAll("SELECT u.name AS name, m.message AS message, DATE_FORMAT(m.created,'%H:%i') AS created FROM messages as m LEFT JOIN 
    users as u on u.id = m.user_id 
                              ORDER BY m.created DESC LIMIT 10");
    }

    public function addBullet($user_id, $x, $y, $vx, $vy)
    {
        return $this->execute("INSERT INTO bullets (bullet_id, x, y, vx, vy)
        VALUES (?,?,?,?,?)", [$user_id, $x, $y, $vx, $vy]);
    }

    public function getBullets()
    {
        return $this->queryAll("SELECT  u.bullet_id AS bullet_id,u.user_id AS user_id, bullet.x AS x,bullet.y AS y,bullet.vx AS vx,bullet.vy AS vy
        FROM bullets as bullet LEFT JOIN usersBullets as u on u.bullet_id = bullet.bullet_id
        ORDER BY u.bullet_id");
    }

    public function DeleteBullet($id)
    {
        $this->execute("DELETE  FROM bullets WHERE id=?", [$id]);
    }

    public function updateScoreInTeam($teamId, $score)
    {

        $this->execute("UPDATE teams SET team_score=team_score+? WHERE  team_id=?", [$score, $teamId]);

    }

    public function getTeamsInfo()

    {
        return $this->queryAll("SELECT t.team_id, user_id, team_score FROM teams as t INNER JOIN userTeams as u on t.team_id = u.team_id GROUP BY t.team_id");

    }

    public function setSkinInLobby($id, $skinId)
    {
        $this->execute("UPDATE userSkins SET skin_id=? WHERE  id=?", [$skinId, $id]);
    }

    // ЖАЛКАЯ ПАРОДИЯ //

    public function getSkinsInLobby()
    {
        return $this->queryAll("SELECT userSkins.skin_id as id, skins.text, skins.image FROM userSkins INNER JOIN skins ON userSkins.skin_id = skins.id WHERE skins.role='lobby'");
    }

    public function getPlayers()
    {
        return $this->queryAll("SELECT u.token, p.x, p.y, p.vx, p.vy, p.dx, p.dy FROM players as p INNER JOIN users as u on u.id = p.user_id");
    }

    public function setPlayer($id, $x, $y, $vx, $vy, $dx, $dy)
    {
        $this->execute("INSERT INTO players (user_id, x, y, vx, vy, dx, dy) VALUES (?, ?, ?, ?, ?, ?, ?) 
ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), x = VALUES(x), y = VALUES(y), vx = VALUES(vx), vy = VALUES(vy), dx = VALUES(dx), dy = VALUES(dy);
", [$id, $x, $y, $vx, $vy, $dx, $dy]);
    }

    public function getObjectById($id)
    {
        return $this->query("SELECT * FROM objects WHERE id=?", [$id]);
    }

    public function getObjects()
    {
        return $this->queryAll("SELECT id, state FROM objects");
    }

    public function setDestroyObject($objectId, $state)
    {
        $this->execute("UPDATE objects SET state=? WHERE id=?", [$state, $objectId]);
    }

    public function getHashes()
    {
        return $this->query("SELECT * FROM game WHERE id=1");
    }

    public function updateChatHash($hash)
    {
        $this->execute("UPDATE game SET chat_hash=? WHERE id=1", [$hash]);
    }

    public function updatePlayersHash($hash)
    {
        $this->execute("UPDATE game SET players_hash=? WHERE id=1", [$hash]);
    }
public function updateBulletsHash($hash)
    {
        $this->execute("UPDATE game SET bullets_hash=? WHERE id=1", [$hash]);
    }
public function updateObjectsHash($hash)
    {
        $this->execute("UPDATE game SET objects_hash=? WHERE id=1", [$hash]);
    }

    public function deletePlayerInPlayers($token)
    {
        $this->execute("DELETE FROM players
WHERE user_id = (SELECT id FROM users WHERE token = ?)", [$token]);
    }

    public function deletePlayerInTeams($token)
    {
        $this->execute("DELETE FROM userTeams
WHERE user_id = (SELECT id FROM users WHERE token = ?)", [$token]);
    }

}

