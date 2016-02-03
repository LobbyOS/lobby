<?php include APP_DIR . "/src/load.php";?>
<div class="top">
  <div class="table">
    <div id="left" class="left">
      <h2>Saves</h2>
      <div>
        <table>
          <thead>
            <tr>
              <td>Name</td>
              <td>Actions</td>
            </tr>
          </thead>
          <tbody id="configSaves"></tbody>
        </table>
      </div>
    </div>
    <div id="right" class="left">
      <h2>Save</h2>
      <a class="button" id="saveConfig">Save Current Configuration</a>
      <h2>Compression Options</h2>
      <form id="options">
        <label>
          <input type="checkbox" data-binding="minHtml" checked="checked" name="minHtml"/>
          <span>Minimize HTML</span>
        </label>
        <label>
          <input type="checkbox" data-binding="minPHP" checked="checked" name="minPHP"/>
          <span>Minimize HTML in .php Files</span>
        </label>
        <label>
          <input type="checkbox" data-binding="noComments" checked="checked" name="noComments"/>
          <span>Remove Comments</span>
        </label>
        <label>
          <input type="checkbox" data-binding="minCss" checked="checked" name="minCss"/>
          <span>Minimize CSS</span>
        </label>
        <label>
          <input type="checkbox" data-binding="minJs" checked="checked" name="minJs"/>
          <span>Minimize JS</span>
        </label>
        <label>
          <input type="checkbox" data-binding="minInline" checked="checked" name="minInline"/>
          <span>Minimize Inline CSS, JS (&lt;script>&lt;/script>, &lt;style>&lt;/style>)</span>
        </label>
      </form>
      <h2>Site Details</h2>
      <form id="siteDetails">
        <label>
          <span>Site Location</span>
          <input type="text" id="site_location" data-binding="siteLoc" name="location" placeholder="/var/www/html/mysite/local" />
          <a data-lobby="filechooser" data-lobby-input=".workspace#site-compressor #site_location">Choose Path</a>
        </label>
        <label>
          <span>Output</span>
          <input type="text" data-binding="siteOutput" name="output" placeholder="/var/www/html/mysite/compressed" />
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
          <p>Run a Terminal command before compression starts (Avoid using double quotes ("))</p>
            <input type="text" data-binding="beforeCommand" placeholder="Type Command Here" name="beforeCommand"/>
          </label>
        </div>
        <div>
          <h2>After Compression</h2>
          <label>
            <p>Run a Terminal command after compression finished (Avoid using double quotes ("))</p>
            <input type="text" data-binding="afterCommand" placeholder="Type Command Here" name="afterCommand"/>
          </label>
        </div>
        <button class="button green">Start Compressing</button>
        <a class="button" id="saveConfig">Save Current Configuration</a>
      </form>
    </div>
   </div>
</div>
<div class="compress-status">
  Compression details will be shown up here after you request for compression.
</div>
