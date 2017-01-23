<div class="center contentbox" style="width: 50%; margin-top: 30px;">
    <div class="boxheader"></div>
    <div class="sidebox_title">Registrieren</div>
    <div class="boxheader"></div>
    <form name="registerform" id="registerform" method="post">
        <table cellpadding="2" cellspacing="0" border="0">
            <tr>
                <td colspan="2" class="center">{registererror}</td>
            </tr>
            <tr>
                <td class="right">Loginname:</td>
                <td><input type="text" id="reg_username" name="reg_username" style="width:250px;" value="{reg_username}"></td>
            </tr>
            <tr>
                <td class="right">Passwort:</td>
                <td><input type="password" id="reg_password" name="reg_password" style="width:250px;" onkeyup="encrypt_password('reg_password', 'reg_hashpw')"><input type="hidden" id="reg_hashpw" name="reg_hashpw"></td>
            </tr>
            <tr>
                <td class="right">Passwort wiederholen:</td>
                <td><input type="password" id="reg_password_verification" name="reg_password_verification" style="width:250px;"></td>
            </tr>
            <tr>
                <td class="right">eMail-Adresse:</td>
                <td><input type="text" id="reg_email" name="reg_email" style="width:250px;" value="{reg_email}"></td>
            </tr>
            <tr>
                <td colspan="2"><br /><br /><input type="button" onclick="get_data('contentbox', 'content&f=register&gid={gid}', 'post', 'registerform', 'true');" value="Registrierung abschicken!"><br /></td>
            </tr>
        </table>
    </form>
</div>