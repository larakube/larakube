// @ts-check
import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';
import sitemap from '@astrojs/sitemap';

// https://astro.build/config
export default defineConfig({
	site: 'https://www.larakube.com',
	integrations: [
		starlight({
			title: 'LaraKube',
			social: {
				github: 'https://github.com/larakube/larakube',
			},
			lastUpdated: true,
			editLink: {
				baseUrl: 'https://github.com/larakube/larakube/edit/main/docs/',
			},
			sidebar: [
				{
					label: 'Guide',
					items: [
						// Each item here is one entry in the navigation menu.
						{ label: 'Getting started', slug: 'guide/getting-started' },
						{ label: 'Configure Laravel', slug: 'guide/configure-laravel' },
						{ label: 'Image', slug: 'guide/image' },
						{ label: 'Ingress', slug: 'guide/ingress' },
						{ label: 'Scheduler', slug: 'guide/scheduler' },
						{ label: 'Deployment', slug: 'guide/deployment' },
					],
				},
				{
					label: 'Examples',
					items: [
						// Each item here is one entry in the navigation menu.
						{ label: 'Laravel Octane', slug: 'examples/laravel-octane' },
					],
				},
				{
					label: 'Reference',
					autogenerate: { directory: 'reference' },
				},
			],
		}),
		sitemap(),
	],
});
