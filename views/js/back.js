/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
function selfValidation(){
    $.ajax({
        url: "../modules/netopia/ajaxSelfValidationRequest.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data: {
            ajax: true,
            controller: 'AdminNetopiaSetup',
            action: 'test', // prestashop already set camel case before execute method
            token: '2a05fbe95902fef8679869776a25cff4'
        },
        success: function(data)
        {
            if (data.status)
            {
                document.getElementById("selfValidationError").style.display = "none";
                document.getElementById("selfValidationSuccess").style.display = "block";
                document.getElementById("selfValidationSuccess").innerHTML = data.msg;
                console.log(data);
            }
            else
            {
                document.getElementById("selfValidationSuccess").style.display = "none";
                document.getElementById("selfValidationError").style.display = "block";
                document.getElementById("selfValidationError").innerHTML = data.msg;
                console.log(data);
            }
        },
        error : function (data){
            document.getElementById("selfValidationError").style.display = "block";
            console.log(data);
        }
    });
}

function sendVerifyRequest(){
    // alert('send Validation Request Function')
    // alert(document.location.pathname)
        $.ajax({
            url: "../modules/netopia/ajaxSendActivationRequest.php",
            type: 'POST',
            cache: false,
            dataType: "json",
            success: function(data)
            {
                if (data.status)
                {
                    document.getElementById("sendActivationError").style.display = "none";
                    document.getElementById("sendActivationSuccess").style.display = "block";
                    document.getElementById("sendActivationSuccess").innerHTML = data.msg;
                    console.log(data);
                }
                else
                {
                    document.getElementById("sendActivationSuccess").style.display = "none";
                    document.getElementById("sendActivationError").style.display = "block";
                    document.getElementById("sendActivationError").innerHTML = data.msg;
                    console.log(data);
                }
            }
        });
}

$( document ).ready(function() {
    console.log( "ready!" );
    // document.getElementById("selfValidationSuccess").style.display = "none";
    // document.getElementById("selfValidationError").style.display = "none";
});


$('#NETOPIA_LIVE_PUB_KEY-name').hide();
// $('#NETOPIA_LIVE_PUB_KEY-name').on('change', function () {
//     // notifyHandle($(this)[0].files[0],'cer',this);
//     alert('Live Public Changed');
// });
//
$('#NETOPIA_LIVE_PRI_KEY-name').on('change', function () {
    // notifyHandle($(this)[0].files[0],'cer',this);
    alert('Live PRIVATE Changed');
});
//
// $('#NETOPIA_SAND_PUB_KEY-name').on('change', function () {
//     // notifyHandle($(this)[0].files[0],'cer',this);
//     alert('SAND Public Changed');
// });
//
// $('#NETOPIA_SNAD_PRI_KEY-name').on('change', function () {
//     // notifyHandle($(this)[0].files[0],'cer',this);
//     alert('SAND PRIVATE Changed');
// });

