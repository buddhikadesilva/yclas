<p class="text-center">
	<b><?=_e('Bitcoin Address')?></b>
	<br>
	<?if(isset($ad->cf_bitcoinaddress) AND !empty($ad->cf_bitcoinaddress)):?>
		<a href="bitcoin:<?=$ad->cf_bitcoinaddress?>">
			<img title="Bitcoin QR code for the address <?=$ad->cf_bitcoinaddress?>" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?=$ad->cf_bitcoinaddress?>">	
		</a>
		<br>
		<a href="bitcoin:<?=$ad->cf_bitcoinaddress?>"><b><?=$ad->cf_bitcoinaddress?></b></a>
	<?elseif(isset($ad->user->cf_bitcoinaddress) AND !empty($ad->user->cf_bitcoinaddress)):?>
		<a href="bitcoin:<?=$ad->user->cf_bitcoinaddress?>">
			<img title="Bitcoin QR code for the address <?=$ad->user->cf_bitcoinaddress?>" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?=$ad->user->cf_bitcoinaddress?>">	
		</a>
		<br>
		<a href="bitcoin:<?=$ad->user->cf_bitcoinaddress?>"><b><?=$ad->user->cf_bitcoinaddress?></b></a>
	<?endif?>
</p>
