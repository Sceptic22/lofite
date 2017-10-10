$(document).ready(function()
{
    $("#form_contact").on('submit',function ()
    {
        $("#btn_send").prop( "disabled", true );
        var url=$("#form_contact").attr("action");
        var name,email,msg;
        name=$("#name").val();
        email=$("#email").val();
        msg=$("#message").val();
        $.ajax({
            type: "POST",
            url: url,
            data: "name="+name+"&email="+email+"&message="+msg,
            success: function(msg)
            {
                $("#btn_send").prop( "disabled", false );
                if (msg.length == 0)
                {
                    $("#name").val("");
                    $("#email").val("");
                    $("#message").val("");

                    $("#textAlert").html("Message has been sent, we will contact you");
                  }
                else
                    $("#textAlert").html(msg);

                $("#openAlert").click();

            }
        });
        return false;
    });
});