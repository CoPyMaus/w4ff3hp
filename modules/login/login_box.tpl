
<div class="sidebox">
        <div class="boxheader"></div>
        <div class="sidebox_title">Login / Anmelden </div>
        <div class="boxheader"></div>
        <div style="margin: 10px">
            <form name="loginform" id="loginform" method="post">
            <table width="100%" border="0">
                <tr>
                    <td><input type="hidden" name="gid" id="gid" value="{gid}"></td>
                </tr>
                <tr>
                    <td align="center"><input type="text" id="username" name="username" value placeholder="Benutzername"> </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="password" id="password" name="password" value placeholder="Password">
                        <input type="hidden" id="hashpw" name="hashpw">
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center"><input type="button" onclick="get_data('sitebox_left', 'sitebox', 'post', 'loginform', 'true');" value="Anmelden"> </td>
                </tr>
            </table>
            </form>
        </div>
        <div style="text-align: center; cursor: pointer;" onclick="get_data('contentbox', 'content&f=register&gid={gid}', 'get', 'register', 'true');">Noch kein Login? Registrieren!</div>
</div>
