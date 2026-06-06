import React, { Component, Fragment } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { __, sprintf } from '@wordpress/i18n';
import { Link } from 'react-router-dom';
import LazyLoad from 'react-lazyload';
import { FixedSizeGrid as Grid } from 'react-window';
import { AppstoreOutlined, BarcodeOutlined, DatabaseOutlined, SearchOutlined, WarningFilled, CheckOutlined } from '@ant-design/icons';
import { getProducts } from '../../../actions/products';
import { getCategories } from '../../../actions/categories';
import { getCustomers } from '../../../actions/customers';
import { getCountriesAndStates } from '../../../actions/countries-and-states';
import { filterProductsByCategory, filterProductsBySearch } from './../../../actions/products';
import Product from './product/product.jsx';
import Popup from './../../popup/popup.jsx';
import { addToCart, getProductViaBarcode } from './../../../actions/cart';
import { getOrders } from './../../../actions/orders';
import { getSettings } from './../../../actions/settings';
import { getTables } from './../../../actions/tables';
import { getPosRoute } from '../../../services/routes';
import { notify } from '../../../services/notifications';
import { getPosConfig } from '../../../services/runtime';
import { isMobileViewport } from '../../../utils/navigation';
import { getOutletId, hasEntries, isSameId } from '../../../utils/value';
import { isEnterKey } from '../../../utils/event';

const getSettingsState = settings => settings || {};
const getHistoryAction = history => history && history.action;
const getProductsStateList = productsState => Array.isArray(productsState && productsState.list) ? productsState.list : [];
const getProductsStateCategory = productsState => productsState && productsState.category !== undefined ? productsState.category : 0;
const getProductsSearchValue = productsState => productsState && productsState.s ? productsState.s : '';
const getEventInputValue = event => event && event.target ? event.target.value : '';

const getCategoryId = match => {
    const categoryId = match && match.params ? match.params.cid : undefined;

    if (categoryId === undefined) {
        return 0;
    }

    return categoryId === 'all' ? 0 : categoryId;
};

const getVisibleProducts = (productsState, categoryId) => {
    if (productsState.s) {
        return productsState.sproducts;
    }

    if (categoryId) {
        return productsState.cproducts;
    }

    return productsState.list;
};

const sortProductsByTitle = products => [...products].sort((firstProduct, secondProduct) => {
    if (firstProduct.title > secondProduct.title) {
        return 1;
    }

    if (secondProduct.title > firstProduct.title) {
        return -1;
    }

    return 0;
});

const buildProductRows = (products, columnCount) => {
    const rows = [];

    (Array.isArray(products) ? products : []).forEach((product, index) => {
        const rowIndex = Math.floor(index / columnCount);

        if (!Array.isArray(rows[rowIndex])) {
            rows[rowIndex] = [];
        }

        rows[rowIndex].push(product);
    });

    return rows;
};

const getProductsListStyle = (displayCategory, component) => (
    displayCategory === 'disabled' ? { height: 'calc(100vh - 95px)' } : { height: 'calc(100vh - 218px)' }
);

const getSearchWrapperStyle = () => (
    { gridTemplateColumns: '60% max-content auto' }
);

const getProductGridItemClassName = (columnIndex, rowIndex) => (
    columnIndex % 2
        ? rowIndex % 2 === 0
            ? 'ddwcpos-grid-item-odd'
            : 'ddwcpos-grid-item-even'
        : rowIndex % 2
            ? 'ddwcpos-grid-item-odd'
            : 'ddwcpos-grid-item-even'
);

const getProductGridLayout = (screenWidth, wrapperWidth) => {
    let columnCount = 6;
    let columnWidth = 260;
    let rowHeight = 134;
    let containerWidth = wrapperWidth || 1000;

    if (isMobileViewport()) {
        containerWidth = screenWidth;
    }

    if ('image_top' === getPosConfig().product_layout) {
        rowHeight = 265;
        columnWidth = 150;

        if (screenWidth >= 2300) {
            columnCount = 9;
            columnWidth = containerWidth / 9.11;
        } else if (screenWidth >= 2100 && screenWidth < 2300) {
            columnCount = 8;
            columnWidth = containerWidth / 8.11;
        } else if (screenWidth >= 1700 && screenWidth < 2100) {
            columnCount = 7;
            columnWidth = containerWidth / 7.15;
        } else if (screenWidth >= 1550 && screenWidth < 1700) {
            columnCount = 6;
            columnWidth = containerWidth / 6.1;
        } else if (screenWidth > 1300 && screenWidth < 1550) {
            columnCount = 5;
            columnWidth = containerWidth / 5.10;
        } else if (screenWidth > 1024 && screenWidth <= 1300) {
            columnCount = 4;
            columnWidth = containerWidth / 4;
        } else if (screenWidth > 650 && screenWidth < 1024) {
            rowHeight = 300;
            columnCount = 4;
            columnWidth = containerWidth / 4;
        } else if (screenWidth > 440 && screenWidth <= 650) {
            rowHeight = 300;
            columnCount = 3;
            columnWidth = containerWidth / 3.09;
        } else {
            rowHeight = 300;
            columnCount = 2;
            columnWidth = containerWidth / 2.09;
        }
    } else if (screenWidth >= 1600) {
        columnCount = 6;
        columnWidth = containerWidth / 6;
    } else if (screenWidth >= 1550 && screenWidth < 1600) {
        columnCount = 5;
        columnWidth = containerWidth / 5;
    } else if (screenWidth > 1300 && screenWidth < 1550) {
        columnCount = 4;
        columnWidth = containerWidth / 4.09;
    } else if (screenWidth > 1024 && screenWidth < 1301) {
        columnCount = 3;
        columnWidth = containerWidth / 3;
    } else {
        columnCount = 2;
        columnWidth = containerWidth / 2.07;
    }

    return {
        columnCount,
        columnWidth,
        rowHeight,
        containerWidth,
    };
};

const getCategoryCardClassName = isActive => (
    isActive ? 'ddwcpos-category-card ddwcpos-category-active' : 'ddwcpos-category-card'
);

const getCategoryCardsLength = categoryElement => (
    Math.floor((categoryElement ? categoryElement.offsetWidth - 10 : 0) / 100)
);

const getProductCardKey = product => product.id || product.product_id || product.slug || product.title;

const getBarcodeResetState = () => ({
    barcodeValue: '',
});
const getProductsWrapperElement = () => document.querySelector('.ddwcpos-products-tab-wrapper');
const getCategoryWrapperElement = () => document.querySelector('.ddwcpos-category-wrapper');
const getGridDimensions = () => {
    const productsWrapper = getProductsWrapperElement();
    const categoryWrapper = getCategoryWrapperElement();
    const gridLayout = getProductGridLayout(window.innerWidth, productsWrapper ? productsWrapper.offsetWidth : 1000);

    return {
        ...gridLayout,
        categoriesCardsLength: getCategoryCardsLength(categoryWrapper),
    };
};

const shouldShowViewAllCategories = (categoriesCardsLength, categoriesHTML) => Array.isArray(categoriesHTML) && categoriesCardsLength < categoriesHTML.length;
const getProductsResultsLabel = products => `${Array.isArray(products) ? products.length : 0} ${__('Results', 'devdiggers-multipos-for-woocommerce')}`;

const getVisibleCategoryCards = (categoriesHTML, categoriesCardsLength) => (
    shouldShowViewAllCategories(categoriesCardsLength, categoriesHTML)
        ? categoriesHTML.slice(0, categoriesCardsLength)
        : categoriesHTML
);
const getGridCellProduct = (productRows, rowIndex, columnIndex) => (
    Array.isArray(productRows[rowIndex]) ? productRows[rowIndex][columnIndex] : undefined
);

const buildCategoryCards = (categories, activeCategoryId, handleHideAllCategoriesPopup) => ([
    <Link key="all" className={getCategoryCardClassName(isSameId(0, activeCategoryId))} onClick={handleHideAllCategoriesPopup} to={getPosRoute('/category/all')}>
        <DatabaseOutlined />
        <p>{__('All', 'devdiggers-multipos-for-woocommerce')}</p>
    </Link>,
    ...(Array.isArray(categories) ? categories : []).map(category => (
        <Link key={category.id} className={getCategoryCardClassName(isSameId(category.id, activeCategoryId))} onClick={handleHideAllCategoriesPopup} to={getPosRoute(`/category/${category.id}`)}>
            {category.image ? <img src={category.image} alt={category.name} width="24" height="24" /> : ''}
            <p title={category.name}>{category.name}</p>
        </Link>
    )),
]);

class Home extends Component {
    constructor(props) {
        super(props);
        this.barcodeBuffer = '';

        this.state = {
            cid: '',
            search: '',
            productsLoaded: false,
            categoryProductsLoaded: false,
            showAllCategoriesPopup: false,
            showBarcodePopup: false,
            barcodeValue: '',
        };
    }

    componentDidMount() {
        const outletId = getOutletId(this.props.outlet);
        this.props.getCategories(outletId);
        this.props.getProducts(outletId).then(res => {
            this.setState({
                productsLoaded: true
            });
        });
        this.props.getCustomers(outletId);
        this.props.getCountriesAndStates(outletId);
        this.props.getTables();
        this.props.getSettings();

        if (getHistoryAction(this.props.history) === 'POP') {
            this.handleHideAllCategoriesPopup();
        }
        window.addEventListener('keypress', this.handleBarcodeKeypress);
    }

    componentWillUnmount() {
        window.removeEventListener('keypress', this.handleBarcodeKeypress);
    }

    handleBarcodeKeypress = e => {
        const target = e.target || e.srcElement;

        if (!target || !target.tagName || target.tagName.toUpperCase() !== 'BODY') {
            return;
        }

        if (isEnterKey(e) && this.barcodeBuffer) {
            this.setState({
                barcodeValue: this.barcodeBuffer
            }, () => {
                this.handleAddProductViaBarcode(e);
                this.barcodeBuffer = '';
            });
            return;
        }

        if (/^\d$/.test(e.key) || /^[a-zA-Z]$/.test(e.key)) {
            this.barcodeBuffer += e.key;
        }
    }

    componentDidUpdate(prevProps) {
        const products = getProductsStateList(this.props.products);
        const prevProducts = getProductsStateList(prevProps.products);
        const cid = getCategoryId(this.props.match);
        const prevCid = getCategoryId(prevProps.match);
        const productsListChanged = prevProducts !== products;
        const categoryStateChanged = !isSameId(getProductsStateCategory(this.props.products), cid);

        if (products.length && this.state.productsLoaded && (productsListChanged || !isSameId(cid, prevCid) || !this.state.categoryProductsLoaded) && categoryStateChanged) {
            this.setState({
                cid: cid,
                categoryProductsLoaded: true
            }, () => {
                this.props.filterProductsByCategory(cid, products);
            });
        }
    }

    handleProductSearch = e => {
        const searchValue = getEventInputValue(e);

        this.setState({
            search: searchValue
        });

        if (true) {
            this.props.filterProductsBySearch(searchValue.toLowerCase(), this.props.products);
        }
    }

    handleToggleShowAllCategories = () => {
        this.setState(prevState => ({
            showAllCategoriesPopup: !prevState.showAllCategoriesPopup
        }));
    }

    handleHideAllCategoriesPopup = () => {
        if (this.state.showAllCategoriesPopup) {
            this.setState({
                showAllCategoriesPopup: false
            });
        }
    }

    handleToggleBarcodePopup = () => {
        this.setState(prevState => ({
            showBarcodePopup: !prevState.showBarcodePopup
        }));
    }

    handleBarcodeInput = e => {
        if (isEnterKey(e)) {
            this.handleAddProductViaBarcode();
        } else {
            const barcodeValue = getEventInputValue(e);
            this.setState({
                barcodeValue
            });
        }
    }

    handleBarcodeFormSubmit = e => this.handleAddProductViaBarcode(e)
    handleBarcodeInputChange = e => this.handleBarcodeInput(e)
    handleProductSearchChange = e => this.handleProductSearch(e)

    renderBarcodePopup = () => {
        if (!this.state.showBarcodePopup) {
            return null;
        }

        const popupContent = (
            <Fragment>
                <h2>{__('Enter/Scan Barcode', 'devdiggers-multipos-for-woocommerce')}</h2>
                <form onSubmit={this.handleBarcodeFormSubmit}>
                    <input type="text" onChange={this.handleBarcodeInputChange} placeholder={__('Enter/Scan Barcode', 'devdiggers-multipos-for-woocommerce')} value={this.state.barcodeValue} autoFocus />
                    <p><i>{__('Press enter after entering barcode to add products.', 'devdiggers-multipos-for-woocommerce')}</i></p>
                </form>
            </Fragment>
        );

        return (
            <Popup
                handleOverlay={this.handleToggleBarcodePopup}
                popupContent={popupContent}
                notDisabled={true}
                hideCancelButton={true}
                singleButton={true}
                successButtonText={<Fragment><CheckOutlined />{__('Done', 'devdiggers-multipos-for-woocommerce')}</Fragment>}
                handleSuccess={this.handleToggleBarcodePopup}
                handleCancel={this.handleToggleBarcodePopup}
            />
        );
    }

    renderAllCategoriesPopup = categoriesHTML => {
        if (!this.state.showAllCategoriesPopup) {
            return null;
        }

        return (
            <Fragment>
                <div className="ddwcpos-popup-overlay" onClick={this.handleToggleShowAllCategories}></div>
                <div className="ddwcpos-all-categories-popup">
                    <div className="ddwcpos-all-categories-popup-content">
                        <h2>{__('All Categories', 'devdiggers-multipos-for-woocommerce')}</h2>
                        <div>
                            {categoriesHTML}
                        </div>
                    </div>
                </div>
            </Fragment>
        );
    }

    renderCategoriesSection = (categories, categoriesCardsLength) => {
        if (!categories.length) {
            return null;
        }

        const allCategoriesHTML = buildCategoryCards(categories, this.state.cid, this.handleHideAllCategoriesPopup);
        const categoriesHTML = getVisibleCategoryCards(allCategoriesHTML, categoriesCardsLength);
        const viewAllCategoriesHTML = shouldShowViewAllCategories(categoriesCardsLength, allCategoriesHTML) ? (
            <Link className="ddwcpos-category-card" to="#" onClick={this.handleToggleShowAllCategories}>
                <AppstoreOutlined />
                <p>{__('View All', 'devdiggers-multipos-for-woocommerce')}</p>
            </Link>
        ) : null;

        return (
            <div className="ddwcpos-category-wrapper">
                <h2>{__('Select Category', 'devdiggers-multipos-for-woocommerce')}</h2>
                <div className="ddwcpos-categories-container">
                    {categoriesHTML}
                    {viewAllCategoriesHTML}
                    {this.renderAllCategoriesPopup(allCategoriesHTML)}
                </div>
            </div>
        );
    }

    handleAddProductViaBarcode = e => {
        if (e) {
            e.preventDefault();
        }

        const barcodeValue = this.state.barcodeValue;

        this.props.getProductViaBarcode(barcodeValue).then(product => {
            if (hasEntries(product)) {
                this.props.addToCart(product.product_id, 1);
            } else {
                notify({
                    title: __('Barcode Error', 'devdiggers-multipos-for-woocommerce'),
                    message: sprintf(__('No product exists with this barcode "%s".', 'devdiggers-multipos-for-woocommerce'), barcodeValue),
                    type: 'danger',
                });
            }
        });

        this.setState(getBarcodeResetState());
    }

    render() {
        const categories = Array.isArray(this.props.categories) ? this.props.categories : [];
        const mainProducts = this.props.products || {};
        const searchText = getProductsSearchValue(mainProducts);
        const activeCategoryId = getCategoryId(this.props.match);
        let products = sortProductsByTitle(getVisibleProducts(mainProducts, activeCategoryId) || []);

        products = products;

        const { columnCount, columnWidth, rowHeight, containerWidth, categoriesCardsLength } = getGridDimensions();

        const pA = buildProductRows(products, columnCount);

        const Cell = ({ columnIndex, rowIndex, style }) => {
            const gridProduct = getGridCellProduct(pA, rowIndex, columnIndex);

            if (gridProduct !== undefined) {

                return (
                    <div
                        className={getProductGridItemClassName(columnIndex, rowIndex)}
                        style={style}
                    >
                        <LazyLoad
                            once={true}
                            key={getProductCardKey(gridProduct)}
                            overflow
                            height={200}
                        >
                            <Product product={gridProduct} outlet={this.props.outlet} notification={this.props.notification} />
                        </LazyLoad>
                    </div>
                )
            }

            return null;
        };

        const barcodePopupHTML = this.renderBarcodePopup();

        const style = getSearchWrapperStyle();

        return (
            <div className="ddwcpos-products-tab-wrapper">
                {(this.props.settings || {}).display_category !== 'disabled' ?
                    this.renderCategoriesSection(categories, categoriesCardsLength)
                    : null}
                <div className="ddwcpos-search-wrapper">
                    <h2>{__('Products', 'devdiggers-multipos-for-woocommerce')}</h2>

                    <div className="ddwcpos-search-input-wrapper" style={style}>
                        <div className="ddwcpos-search-input">
                            <SearchOutlined />
                            <input type="text" className="ddwcpos-form-control" value={this.state.search} placeholder={__('Search Product by title, ID, SKU or Barcode Number', 'devdiggers-multipos-for-woocommerce')} onChange={this.handleProductSearchChange} autoComplete="off" />
                        </div>
                        <div className="ddwcpos-icon-card ddwcpos-barcode-icon" onClick={this.handleToggleBarcodePopup} title={__('Add Product via Barcode', 'devdiggers-multipos-for-woocommerce')}>
                            <BarcodeOutlined />
                        </div>
                        <span>{getProductsResultsLabel(products)}</span>
                    </div>

                    {barcodePopupHTML}
                </div>
                {null}
                {products.length > 0 ?
                    <Grid
                        className="ddwcpos-grid ddwcpos-products-list"
                        columnCount={columnCount}
                        columnWidth={columnWidth}
                        height={1000}
                        rowCount={pA.length}
                        rowHeight={rowHeight}
                        width={containerWidth}
                        style={getProductsListStyle((this.props.settings || {}).display_category, this)}
                    >
                        {Cell}
                    </Grid>
                    :
                    <div className="ddwcpos-no-results">
                        <WarningFilled />
                        <p>{__('No Products Found', 'devdiggers-multipos-for-woocommerce')}</p>
                    </div>
                }
            </div>
        );
    }
}

const mapStateToProps = state => ({
    categories: state.categories,
    products: state.products,
    settings: state.settings,
});

const mapDispatchToProps = dispatch => bindActionCreators({
    getProducts,
    getCategories,
    getCustomers,
    getCountriesAndStates,
    filterProductsByCategory,
    filterProductsBySearch,
    getProductViaBarcode,
    addToCart,
    getOrders,
    getSettings,
    getTables
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Home);
