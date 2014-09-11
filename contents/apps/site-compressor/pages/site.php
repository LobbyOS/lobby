<?php include APP_DIR . "/load.php";?>
<div class="top">
	<div class="table">
		<div class="left">
			<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
			<h2>Site Details</h2>
			<form id="siteDetails">
				<label>
					<span>Site Location</span>
					<input type="text" data-binding="siteLoc" name="location"/>
					<p>The site's source code full absolute location</p>
				</label>
				<label>
					<span>Output</span>
					<input type="text" data-binding="siteOutput" name="output"/>
					<p>The location where the output must be written</p>
      			</label>
      			<h2>Replacer</h2>
				<div id="replaceFields">       
					<p>You can also replace strings like <b>localsite.dev</b> to <b>mydomain.com</b></p>
					<a class="addReplaceField button">Add New Field</a>
				</div>
				<div>
					<h2>Before Compression</h2>
					<label>
						<input type="text" data-binding="beforeCommand" placeholder="Type Command Here" name="beforeCommand"/>
						<p>Run a Terminal command before compression starts</p>
						<p>Avoid using double quotes (")</p>
					</label>
				</div>
				<div>
					<h2>After Compression</h2>
					<label>
						<input type="text" data-binding="afterCommand" placeholder="Type Command Here" name="afterCommand"/>
						<p>Run a Terminal command after compression finished</p>
						<p>Avoid using double quotes (")</p>
					</label>
				</div>
				<button class="button">Let's Start Compressing</button>
			</form>
		</div>
		<div class="right">
			<h2>Compression Options</h2>
			<form id="options">
				<label>
					<input type="checkbox" data-binding="minHtml" checked="checked" name="minHtml"/>
					Minimize HTML
				</label>
				<label>
					<input type="checkbox" data-binding="minPHP" checked="checked" name="minPHP"/>
					Minimize HTML in .php Files
				</label>
				<label>
					<input type="checkbox" data-binding="noComments" checked="checked" name="noComments"/>
					Remove Comments
				</label>
				<label>
					<input type="checkbox" data-binding="minCss" checked="checked" name="minCss"/>
					Minimize CSS
				</label>
				<label>
					<input type="checkbox" data-binding="minJs" checked="checked" name="minJs"/>
					Minimize JS
				</label>
				<label>
					<input type="checkbox" data-binding="minInline" checked="checked" name="minInline"/>
					Minimize Inline CSS, JS (&lt;script>&lt;/script>, &lt;style>&lt;/style>)
				</label>      
			</form>
			<a class="button" id="saveConfig">Save Current Configuration</a>
			<p>(including Site & Replacer Details)</p>
			<h2>Saves</h2>
			<div id="configSaves"></div>
		</div>
   </div>
</div>
<div class="compress-status">
	Compression details will be shown up here after you request for compression.
</div>