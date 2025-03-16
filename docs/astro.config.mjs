// @ts-check
import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';

// https://astro.build/config
export default defineConfig({
	integrations: [
		starlight({
			title: 'LaraKube',
			social: {
				github: 'https://github.com/solidtime-io/larakube',
			},
			lastUpdated: true,
			editLink: {
				baseUrl: 'https://github.com/solidtime-io/larakube/edit/main/docs/',
			},
			sidebar: [
				{
					label: 'Guide',
					items: [
						// Each item here is one entry in the navigation menu.
						{ label: 'Getting started', slug: 'guide/getting-started' },
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
	],
});
