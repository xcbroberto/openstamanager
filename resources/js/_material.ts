import '@material/mwc-button';
import '@material/mwc-icon-button';
import '@material/mwc-list';
import '@material/mwc-menu';
import './WebComponents/TopAppBar';
import './WebComponents/MaterialDrawer';

import {Inertia} from '@inertiajs/inertia';
import type {Dialog as MWCDialog} from '@material/mwc-dialog';
import type {Drawer as MWCDrawer} from '@material/mwc-drawer';
import type {ListItem as MWCListItem} from '@material/mwc-list/mwc-list-item';
import type {Menu as MWCMenu} from '@material/mwc-menu';
import $, {
  type Cash,
  type Element
} from 'cash-dom';


// Remove the ugly underline under mwc button text when inside <a> tags.
$('a').has('mwc-button').css('text-decoration', 'none');

// Submit forms with enter key
$('mwc-button[type="submit"], mwc-icon-button[type="submit"]')
  .closest('form')
  .find('text-field, select, text-area')
  .on('keydown', function (this: Cash, event: KeyboardEvent) {
    if (event.key === 'Enter') {
      event.preventDefault();

      $(this).closest('form')
        .find('mwc-button[type=submit], mwc-icon-button[type="submit"]')
        .trigger('click');
    }
  });

const drawer: MWCDrawer | null = document.querySelector('material-drawer');
if (drawer && drawer.parentElement) {
  drawer.parentElement.addEventListener('MDCTopAppBar:nav', () => {
    drawer.open = !drawer.open;
  });

  // Drawer items click
  $(drawer).find('a.drawer-item').on('click', function (this: HTMLAnchorElement, event: PointerEvent) {
    event.preventDefault();

    Inertia.visit(this.href);

    const drawerItem: Element & Partial<MWCListItem> | null = this.firstElementChild;
    if (drawerItem) {
      drawerItem.activated = true;
    }

    $(this).siblings('.drawer-item')
      .filter((index, item) => $(item).has('mwc-list-item[activated]').length > 0)
      .find('mwc-list-item')
      .prop('activated', false);
  });
}

$('mwc-menu').each((index, menu: HTMLElement & Partial<MWCMenu>) => {
  const trigger: string | null = menu.getAttribute('trigger');
  const button = trigger ? $(`#${trigger}`) : $(menu).prev();
  button.on('click', () => {
    menu.open = !menu.open;
  });
  menu.anchor = button.get(0);
});

$('mwc-dialog').each((index, dialog: HTMLElement & Partial<MWCDialog>) => {
  const trigger = dialog.getAttribute('trigger');
  const button = trigger ? $(`#${trigger}`) : $(dialog).prev('mwc-dialog-button');
  if (button) {
    button.on('click', () => {
      (dialog as MWCDialog).show();
    });
  }
});