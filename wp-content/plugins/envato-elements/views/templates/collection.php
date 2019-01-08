<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script id="tmpl-envato-elements__collection-template-cell" type="text/x-handlebars-template">
	<div class="envato-elements__collection-template-cell {{#if templateInstalled}} imported{{/if}} {{#if templateInserted.length}} imported{{/if}}">
		<div class="envato-elements__collection-template">
			<div class="envato-elements__template-features">
				{{#if templateInstalled}}
				<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
				{{else}}
				{{#if templateInserted.length}}
				<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
				{{/if}}
				{{/if}}
				{{#each templateFeatures}}
				<span class="envato-elements__template-feature envato-elements__template-feature--{{@key}}">{{small}}</span>
				{{/each}}
			</div>
			<a href="{{templateUrl}}"
				data-nav-type="template"
				data-category-slug="{{categorySlug}}"
				data-collection-id="{{collectionId}}"
				data-template-id="{{templateId}}"
				data-thumb-height="{{previewThumbHeight}}"
				class="envato-elements__collection-template-thumb js-envato-elements__thumb-scroll envato-elements--action"
				data-src="{{previewThumb}}"
				style="padding-bottom: {{previewThumbAspect}};"
			>&nbsp;</a>
			<span class="envato-elements__collection-template-label">{{templateName}}</span>
		</div>
	</div>
</script>

<script id="tmpl-envato-elements__collection-preview" type="text/x-handlebars-template">
	<section class="envato-elements__collection-detail-thumbnail">
		<div class="envato-elements__template-features">
			{{#if templateInstalled}}
			<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
			{{else}}
			{{#if templateInserted.length}}
			<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
			{{/if}}
			{{/if}}
			{{#each templateFeatures}}
			<span class="envato-elements__template-feature envato-elements__template-feature--{{@key}}">{{large}}</span>
			{{/each}}
		</div>

		{{#if largeThumb.src}}
		<div class="envato-elements__collection-preview-placeholder">
			<img
				src="{{largeThumb.src}}"
				width="{{largeThumb.width}}"
				height="{{largeThumb.height}}"
				class="envato-elements__collection-preview-large-img"
				onload="this.parentNode.className = this.parentNode.className + ' --loaded';"
			/>
			<div class="envato-elements__collection-preview-placeholder-img-wrap">
				<img
					src="{{previewThumb}}"
					width="{{largeThumb.width}}"
					height="{{largeThumb.height}}"
					class="envato-elements__collection-preview-placeholder-img"
				/>
			</div>
		</div>
		{{else}}
		<div class="envato-elements__collection-preview-placeholder"></div>
		{{/if}}
	</section>
	<section class="envato-elements__collection-detail-actions">
		<button type="button"
			data-nav-type="collection-close"
			data-category-slug="{{categorySlug}}"
			class="envato-elements__collection-preview-close envato-elements--action"></button>

		<p class="envato-elements__collection-preview-label">Page Template:</p>
		<h3 class="envato-elements__collection-preview-title">{{templateName}}</h3>
		<hr>
		{{#if templateError}}
		<div class="envato-elements__collection-template-options">
			<div class="envato-elements-notice envato-elements-notice--warning">

				{{#each templateMissingPlugins}}
				<div class="envato-elements__collection-template-option envato-elements__collection-template-option--edit-template">

					{{#if_eq slug "elementor-pro"}}
					<img src="<?php echo esc_url( ENVATO_ELEMENTS_URI . 'assets/images/elementor-pro.png' ); ?>" width="200"/>
					<p>This Template requires Elementor Pro. Before you can import the template you'll need to buy, install and activate
						<strong> Elementor Pro</strong>.</p>
					<a href="{{{url}}}" class="button button-primary" target="_blank">{{text}}</a>
					{{else}}
					<p>To use this template please ensure all required plugins are installed and active. </p>
					<a href="{{{url}}}" class="button button-primary">{{text}}</a>
					{{/if_eq}}
				</div>
				{{/each}}
			</div>

		</div>

		{{else}}

		{{#if_eq importMode "insert"}}

			<div class="envato-elements__collection-template-options">

				<div class="envato-elements__collection-template-option envato-elements__collection-template-option--edit-template">
					{{#if templateFeatures.elementor-pro}}
					<div class="envato-elements__collection-template-option--help-text">
						<p>This template includes features from</p>
						<img src="<?php echo esc_url( ENVATO_ELEMENTS_URI . 'assets/images/elementor-pro.png' ); ?>" width="200"/>
					</div>
					{{/if}}

					<button
						data-nav-type="direct-insert-template"
						data-category-slug="{{categorySlug}}"
						data-collection-id="{{collectionId}}"
						data-template-id="{{templateId}}"
						class="button envato-elements-import-button envato-elements--action button-primary"
					>Insert Template <span></span></button>

				</div>
			</div>
		{{else}}

			<div class="envato-elements__collection-template-options">

				<div class="envato-elements__collection-template-option envato-elements__collection-template-option--edit-template">


					{{#if (eq categorySlug "elementor")}}

					{{#if templateFeatures.elementor-pro}}
					<div class="envato-elements__collection-template-option--help-text">
						<p>This template includes features from</p>
						<img src="<?php echo esc_url( ENVATO_ELEMENTS_URI . 'assets/images/elementor-pro.png' ); ?>" width="200"/>
					</div>
					{{/if}}


					{{#if templateInstalled}}

					<div class="envato-elements__collection-template-option--help-text">
						<p>This template has been imported, it is available in your Elementor
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library' ) ); ?>">My Templates</a> list for future use.
						</p>
					</div>
					<a href="{{templateInstalledURL}}" class="button button-primary" target="_blank">{{templateInstalledText}}</a>

					{{else}}

					<div class="envato-elements__collection-template-option--help-text">
						<p>Import this template to make it available in your Elementor
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library' ) ); ?>">My Templates</a> list for future use.
						</p>
					</div>

					<button
						data-nav-type="import-template"
						data-category-slug="{{categorySlug}}"
						data-collection-id="{{collectionId}}"
						data-template-id="{{templateId}}"
						class="button envato-elements-import-button envato-elements--action button-primary"
					>{{templateImportText}} <span></span></button>

					{{/if}}
				</div>
				{{/if}}


				{{#if (eq categorySlug "elementor")}}
				<div class="envato-elements__collection-template-option envato-elements__collection-template-option--divider">
					OR
				</div>
				{{/if}}


				{{#if (or
				(eq categorySlug "elementor")
				(eq categorySlug "beaver-builder")
				)}}
				<div class="envato-elements__collection-template-option envato-elements__collection-template-option--create-page">


					<div class="envato-elements__collection-template-option--help-text">
						<p>Create a new page from this template to make it available as a draft page in your Pages list.</p>
					</div>

					<input type="text" name="insert-template-page-name"
						class="envato-elements__create-page-name"
						data-category-slug="{{categorySlug}}"
						data-collection-id="{{collectionId}}"
						data-template-id="{{templateId}}"
						placeholder="Enter a Page Name">
					<br/>
					<button type="button"
						data-nav-type="insert-template-create-page"
						data-category-slug="{{categorySlug}}"
						data-collection-id="{{collectionId}}"
						data-template-id="{{templateId}}"
						class="button envato-elements-insert-button envato-elements--action">
						Create New Page
						<span></span>
					</button>

					{{#if templateInserted}}

					<div class="envato-elements__collection-template-option--help-text">
						<p><br>Pages created with this Template:
							{{#each templateInserted}}
							<a href="{{pageUrl}}" target="_blank">{{pageName}}</a>
							{{/each}}
						</p>
					</div>
					{{/if}}

				</div>
				{{/if}}

			</div>

		{{/if_eq}}
		{{/if}}

	</section>
</script>

