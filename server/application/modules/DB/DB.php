<?php
require_once __DIR__ . '/Config/Config.php';

class DB
{
    //сохраняет соединение с ДБ
    private $pdo;

    //вызов соединения с БД
    public function __construct()
    {
//        PROD
        $host = Config::$configProd['host'];
        $port = Config::$configProd['port'];
        $user = Config::$configProd['user'];
        $pass = Config::$configProd['pass'];
        $db = Config::$configProd['db'];


//        LOCAL
        /*
        $host = Config::$configLocal['host'];
        $port = Config::$configLocal['port'];
        $user = Config::$configLocal['user'];
        $pass = Config::$configLocal['pass'];
        $db   = Config::$configLocal['db'];
        */
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

    public function getMessage()
    {
        return $this->queryAll("SELECT u.name AS name, m.message AS message,
       DATE_FORMAT(m.created,'%H:%i') AS created FROM messages as m LEFT JOIN 
    users as u on u.id = m.user_id 
                              ORDER BY m.created DESC LIMIT 10");
    }

    public function sendMessage($id, $message)
    {
        $this->execute('INSERT INTO messages (user_id, message, created)
VALUES (?,?, now())', [$id, $message]);
    }


    public function getBullets()
    {
        return $this->queryAll("SELECT  u.bullet_id AS bullet_id,u.user_id AS user_id, 
        b.x AS x,b.y AS y,b.vx AS vx,b.vy AS vy
        FROM bullets as b LEFT JOIN usersBullets as u on u.bullet_id = b.id
        ORDER BY u.bullet_id");
    }

    public function setBullet($x, $y, $vx, $vy)
    {

        $this->execute("INSERT INTO bullets (x,y,vx,vy) VALUES (?,?,?,?)",
            [$x, $y, $vx, $vy]);
    }

    public function DeleteBullet($id)
    {
        $this->execute("DELETE  FROM bullets WHERE id=?", [$id]);
    }

    public function getTeamsInfo()

    {
        return $this->queryAll("SELECT u.id AS bullet_id, u.user_id AS user_id,
       b.x AS x, b.y AS y,
       b.vx AS vx, b.vy AS vy
FROM bullets as b
         LEFT JOIN usersBullets as u on u.bullet_id = b.id
ORDER BY u.bullet_id");

    }

    public function updateScoreInTeam($teamId, $score)
    {

        $this->execute("UPDATE teams SET team_score=team_score+? WHERE  team_id=?",
            [$score, $teamId]);

    }

    public function addPlayerToTeam($id, $teamId)
    {
        $this->execute("INSERT INTO userTeams (user_id, team_id)
VALUES (?, ?)
ON DUPLICATE KEY UPDATE team_id = VALUES(team_id)", [$id,$teamId]);
    }


    public function deletePlayerInTeams($token)
    {
        $this->execute("DELETE FROM userTeams
WHERE user_id = (SELECT id FROM users WHERE token = ?)", [$token]);
    }

    public function deletePlayerInPlayers($token)
    {
        $this->execute("DELETE FROM players
WHERE user_id = (SELECT id FROM users WHERE token = ?)", [$token]);
    }

    public function getSkinsInLobby()
    {
        return $this->queryAll("SELECT userSkins.skin_id as id, skins.text, 
       skins.image FROM userSkins INNER JOIN skins ON userSkins.skin_id = skins.id 
                   WHERE skins.role='lobby'");
    }

    public function setSkinInLobby($id, $skinId)
    {
        $this->execute("UPDATE userSkins SET skin_id=? WHERE  id=?", [$skinId, $id]);
    }


    public function getPlayers()
    {
        return $this->queryAll("SELECT u.token, p.x, p.y, p.vx, p.vy, p.dx, p.dy 
FROM players as p INNER JOIN users as u on u.id = p.user_id");
    }

    public function setPlayer($id, $x, $y, $vx, $vy, $dx, $dy)
    {
        $this->execute("INSERT INTO players (user_id, x, y, vx, vy, dx, dy) VALUES (?, ?, ?, ?, ?, ?, ?) 
ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), x = VALUES(x), y = VALUES(y), 
      vx = VALUES(vx), vy = VALUES(vy), dx = VALUES(dx), dy = VALUES(dy);
", [$id, $x, $y, $vx, $vy, $dx, $dy]);
    }


    public function getObjects()
    {
        return $this->queryAll("SELECT id, state FROM objects");
    }

    public function getObjectById($id)
    {
        return $this->query("SELECT * FROM objects WHERE id=?", [$id]);
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
public function updateSkinsHash($hash){
        $this->execute("UPDATE game SET chat_hash = ? WHERE id = 1", [$hash]);
}

}

