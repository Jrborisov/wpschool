import { modal } from './modal';

class Helper {
  constructor() {
  }

  pageLoaded = () => {
  };

  navigationChange = ( admin, query, action ) => {
    if ( query && query.navType && 'help-modal' === query.navType ) {
      this.loadHelpModal();
    }else if ( query && query.navType && 'terms-modal' === query.navType ) {
      this.loadTermsModal();
    }
  };

  loadHelpModal = () => {
    modal.openModal( 'tmpl-envato-elements__help-modal', {} );
  };

  loadTermsModal = () => {
    modal.openModal( 'tmpl-envato-elements__terms-modal', {} );
  };

}

export let helper = new Helper();
