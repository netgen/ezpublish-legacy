<div id="dashboard-logo"></div>
<div id="mainteinance-text">
    <h2>{'Software update and Maintenance'|i18n( 'design/admin/dashboard/maintenance' )}</h2>

    <p>{'Your installation: <span id="ez-version">%1</span>'|i18n( 'design/admin/dashboard/maintenance', , array( fetch( 'setup', 'alias' ) ) )}</p>
    <p>{'You are running <span class="edition-info">%edition</span>, it might not be up to date with the latest hot fixes and service packs. Contact <a href="%ez_link">Netgen</a> for more infomation.'|i18n( 'design/admin/dashboard/maintenance', , hash( '%edition', fetch( 'setup', 'edition' ), '%ez_link', "https://netgen.io" ) )}</p>
</div>
