{*
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
*}

<div class="panel">
	<div class="row netopia-header">
		<img src="https://suport.mobilpay.ro/np-logo-blue.svg" class="col-xs-6 col-md-4 text-center" id="payment-logo" style="width: 280px;" />
		<div class="col-xs-6 col-md-4 text-center">
			<h4>{l s='NETOPIA Payments system' mod='netopia'}</h4>
			<h4>{l s='Fast - Secure - Reliable' mod='netopia'}</h4>
		</div>
		<div class="col-xs-12 col-md-4 text-center">
			<a href="https://netopia-payments.com/register/" target="_blank" class="btn btn-primary" id="create-account-btn">{l s='Create an NETOPIA account now!' mod='netopia'}</a><br />
			{l s='Already have an account?' mod='netopia'}<a href="https://admin.mobilpay.ro/" target="_blank" > {l s='Log in' mod='netopia'}</a>
		</div>
	</div>

	<hr />
	
	<div class="netopia-content">
		<div class="row">
			<div class="col-md-5">
				<h5>{l s='Avantaje' mod='netopia'}</h5>
				<dl>
					<dt>&middot; {l s='Siguranța tranzacțiilor este prioritatea noastră: utilizăm mediul de tranzacționare 3D Secure' mod='netopia'}</dt>
					<dd>{l s='' mod='netopia'}</dd>
					
					<dt>&middot; {l s='Eficientizăm timpii alocați gestionării platformei de plată cu cardul, pe care o poți implementa rapid, în 3 pași simpli' mod='netopia'}</dt>
					<dd>{l s='' mod='netopia'}</dd>
					
					<dt>&middot; {l s='Asigurăm fidelizarea clienților tăi prin integrarea serviciului de plată recurentă pentru abonamente' mod='netopia'}</dt>
					<dd>{l s='' mod='netopia'}</dd>

					<dt>&middot; {l s='Facilităm accesul clienților la serviciile și produsele tale' mod='netopia'}</dt>
					<dd>{l s='' mod='netopia'}</dd>

					<dt>&middot; {l s='Modulele de plată online sunt foarte simplu de integrat în website' mod='netopia'}</dt>
					<dd>{l s='' mod='netopia'}</dd>

					<dt>&middot; {l s='plata recurentă, prin care poți încasa sume variate, la intervale aleatorii;' mod='netopia'}</dt>
					<dd>{l s='' mod='netopia'}</dd>
				</dl>
			</div>
			
			<div class="col-md-3">
				<h5>{l s='Self Validation' mod='netopia'}</h5>
				<a href="#" class="btn btn-primary" id="self-validation-btn" onclick="selfValidation();">{l s='Self Validation' mod='netopia'}</a>
				<br />
				<em class="text-muted small">
					* {l s='A tools to verify by yourself before send request to NETOPIA Payments.' mod='netopia'}
				</em>
				<div id="selfValidationSuccess" class="alert alert-success" role="alert">
					This is a success alert—check it out!
				</div>
				<div id="selfValidationError" class="alert alert-danger" role="alert">
					This is a danger alert—check it out!
				</div>
			</div>
			<div class="col-md-4">
				<h5>{l s='Send request to NETOPIA Payments to verify' mod='netopia'}</h5>
				<a href="#"  class="btn btn-primary" id="send-validation-ntp-btn" onclick="sendVerifyRequest();">Send request to NETOPIA Payments to verify</a>
				<br />
				<em class="text-muted small">
					* {l s='Send a request to NETOPIA Payments, for verification of your website' mod='netopia'}
				</em>
				<div id="sendActivationSuccess" class="alert alert-success" role="alert">
					This is a success - Send Validation!
				</div>
				<div id="sendActivationError"class="alert alert-danger" role="alert">
					This is a danger Send Validation!
				</div>
			</div>
		</div>

		<hr />
		
		<div class="row">
			<div class="col-md-12">
				<h4>{l s='Accept payments in all major credit cards in Romania' mod='netopia'}</h4>
				
				<div class="row">
					<img src="https://netopia-payments.com/core/assets/5993428bab/images/svg/visa.svg" class="col-md-6" id="visa-logo" style="width: 150px;" />
					<img src="https://netopia-payments.com/core/assets/5993428bab/images/svg/mastercard.svg" class="col-md-6" id="master-logo" style="width: 120px;" />
					<div class="col-md-6">
						<h6 class="text-branded">{l s=''}</h6>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
