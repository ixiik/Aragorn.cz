<?php
/**
 * Description of UserAuthenticator
 *
 * @author Buri
 */

class AraIdentity extends NIdentity implements IIdentity {
    //put your code here


    public function __constuct($id = 0, $username = "Host", $roles = null, $data = null){
        $this->id = $id;
        $this->username = $username;
        parent::__construct($id, $roles, $data);
    }
/*    public function getId(){
        return $this->id;
    }

    public function getRoles(){
        return array();
    }
*/
    public function getUsername(){ return $this->username; }
}

class BanAuthenticationException extends NAuthenticationException{
    private $bdata;
    public function  __construct($message, $code, $ban, $previous = null) {
        $this->bdata = $ban;
        parent::__construct($message, $code, $previous);
    }

    public function getBan(){
        return $this->bdata;
    }
};

class UserAuthenticator extends NObject implements IAuthenticator{
    //put your code here
    const BANNED = 6;
    public function authenticate(array $credentials)
    {
        $username = $credentials[self::USERNAME];
        $password = sha1($credentials[self::PASSWORD]);
    
        // přečteme záznam o uživateli z databáze
        $usrs = DB::users()->select("username,id")->where("username LIKE ?", $username);
        if (!$usrs->count()) { // uživatel nenalezen?
            throw new NAuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
        }
        $row = $usrs->fetch();
        $usr = $row->users_profiles()->select("password")->fetch();
        if ($usr["password"] !== $password) { // hesla se neshodují?
            throw new NAuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
        }
        $bans = DB::bans()->where('user = ? OR ip = ?', array($usr['id'], $_SERVER["REMOTE_ADDR"]))->where('expires >= ?', time())->order('expires DESC');

        foreach($bans as $ban){
            $x = DB::users('id = ?', $ban["author"])->select("username")->fetch();
            $y = DB::users('id = ?', $ban["user"])->select("username")->fetch();
            throw new BanAuthenticationException((string)$x["username"] . ";" . (string)$y["username"], self::BANNED, $ban);
        }

        return new NIdentity($row["id"], null, array("username" => $row["username"])); // vrátíme identitu
    }
}
