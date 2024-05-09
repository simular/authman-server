
import fusion, { sass, babel, parallel, wait, ts, src, symlink } from '@windwalker-io/fusion';
import { syncModuleScripts, installVendors, findModules } from '@windwalker-io/core';

export async function mainCSS() {
  // Watch start
  fusion.watch([
    'resources/assets/scss/front/**/*.scss',
    'src/Module/Front/**/assets/*.scss',
    ...findModules('**/assets/*.scss')
  ]);
  // Watch end

  return wait(
    // Front
    sass(
      [
        'resources/assets/scss/front/main.scss',
        ...findModules('Front/**/assets/*.scss'),
        'src/Module/Front/**/assets/*.scss'
      ],
      'www/assets/css/front/main.css'
    ),
  );
}

export async function adminCSS() {
  // Watch start
  fusion.watch([
    'resources/assets/scss/admin/**/*.scss',
    'src/Module/Admin/**/assets/*.scss',
    ...findModules('**/assets/*.scss')
  ]);
  // Watch end

  return wait(
    // Admin
    sass(
      [
        'resources/assets/scss/admin/main.scss',
        ...findModules('Admin/**/assets/*.scss'),
        'src/Module/Admin/**/assets/*.scss'
      ],
      'www/assets/css/admin/main.css'
    )
  );
}

export async function bootstrap() {
  // Watch start
  fusion.watch('resources/assets/scss/front/_variables.scss');
  // Watch end

  return wait(
    // Front
    sass(
      'resources/assets/scss/front/bootstrap.scss',
      'www/assets/css/front/bootstrap.css'
    )
  );
}

export async function css() {
  return wait(
    // Front
    mainCSS(),
    // Boostrap
    bootstrap(),
    // Admin
    adminCSS(),
  );
}

export async function js() {
  // Watch start
  fusion.watch([
    'resources/assets/src/**/*.{js,mjs,ts}',
    'src/Module/**/assets/**/*.{js,mjs,ts}',
    ...findModules('**/assets/*.{js,mjs,ts}')
  ]);
  // Watch end

  // Compile Start
  return wait(
    babel('resources/assets/src/**/*.{js,mjs}', 'www/assets/js/', { module: 'systemjs' }),
    ts('resources/assets/src/**/*.ts', 'www/assets/js/', { tsconfig: 'tsconfig.js.json' }),
    syncJS()
  );
  // Compile end
}

export async function syncJS() {
  // Compile Start
  return wait(
    ...syncModuleScripts(
      'src/Module',
      'www/assets/js/view/',
      {
        ts: {
          tsconfig: 'tsconfig.js.json'
        }
      }
    )
  );
  // Compile end
}

export async function images() {
  // Watch start
  fusion.watch('resources/assets/images/**/*');
  // Watch end

  // Compile Start
  return wait(
    fusion.copy('resources/assets/images/**/*', 'www/assets/images/')
  );
  // Compile end
}

export async function install() {
  installVendors(
    [
      '@fortawesome/fontawesome-free',
      'wowjs',
      'animate.css',
      'jarallax',
      'swiper',
      'youtube-background',
    ]
  );
}

export default parallel(css, js, images);
