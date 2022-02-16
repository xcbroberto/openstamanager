import {TopAppBar as MWCTopAppBar} from '@material/mwc-top-app-bar';
import {css} from 'lit';
import {customElement} from 'lit/decorators.js';

@customElement('top-app-bar')
export default class TopAppBar extends MWCTopAppBar {
  static styles = [...MWCTopAppBar.styles, css`
    header.mdc-top-app-bar {
      border-bottom: 1px solid var(--mdc-theme-outline-color, #e0e0e0);
      z-index: 7;
    }
  `];
}
