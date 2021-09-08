import Component from '../Component';

export default class Content extends Component {
  view(vnode) {
    this.attrs.addClassNames('mdc-card__content');
    return (
      <div {...this.attrs.all()}>
        {vnode.children}
      </div>
    );
  }
}