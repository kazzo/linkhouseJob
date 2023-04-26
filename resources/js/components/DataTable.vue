<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
            
                <div class="datatable-title my-2">{{ title }}</div>
                
                <div class="form-group row my-2">
                        <label 
                            for="szukaj" 
                            class="col-sm-2 col-form-label">Szukaj nazwy lub loginu</label>
                        <div 
                            class="col-sm-10"><input 
                                                    class="form-control" 
                                                    id="szukaj" 
                                                    @input="filter($event)" 
                                                    v-model="szukaj" 
                                                    ref="searchInput" />
                        </div>
                </div>
                
                <div class="datatable-body table-responsive">
                    <table class="table table-striped">
                    
                        <thead class="table-dark">
                            <tr>                                
                                <th 
                                    v-for="column in columns"                                      
                                    class=""
                                    @click="sortColumn(column);"
                                ><span v-show="sort_column===column"
                                ><span 
                                    v-show="sort_direction==='up'"
                                    >&#x25B2;</span
                                ><span 
                                    v-show="sort_direction==='down'"
                                >&#x25BC;</span></span><span>{{ columnTitle(column) }}</span>                                                                                              
                                </th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr v-if="tableData.length === 0" :style="{ height: (2.6 * rows) + 'rem' }">
                            
                                <td 
                                    class="text-center" 
                                    :colspan="columns.length + 1">
                                    <div  
                                        class="alert alert-primary" 
                                        role="alert">
                                        <div 
                                            v-if="loading" 
                                            class="spinner-border text-success" 
                                            style="width: 3rem; height: 3rem;" 
                                            role="status">
                                        </div>
                                        <div v-else>{{ table_message }}</div>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr 
                                v-for="(data, key1) in tableData" 
                                :key="data.id" 
                                class="datatable-row" 
                                v-else>
                                <td v-for="(value, key) in data">{{ value }}</td>
                                <td class="text-center"
                                ><button 
                                    v-if="data.id!==''" 
                                    type="button" 
                                    @click="sendID(data.id);" 
                                    class="btn btn-primary">Wyślij</button
                                ></td>
                            </tr>
                        </tbody>

                    </table>                    
                </div>        
                
                <div class="container">
                    <div class="form-group row">
                    
                        <div class="col-md-4">
                            <div class="m-2 text-center row"
                                ><label 
                                    for="pagination" 
                                    class="col-form-label col-md-6">Strona</label
                                >
                                <div class="col-md-6">                                 
                                    <select 
                                        id="pagination" 
                                        @change="changePage($event)" 
                                        v-model="page" 
                                        class="form-control form-select">
                                            <option 
                                                v-for="index in num_pages" 
                                                v-bind:value="index">{{ index }}</option
                                            >
                                    </select>
                                </div>
                            </div>
                        </div>
                            
                        <div class="col-md-8 text-right">
                            <button 
                                type="button" 
                                id="reset" 
                                @click="getData();" 
                                class="btn btn-primary m-2">RESET</button
                            >
                            <button 
                                type="button" 
                                id="change-api-key" 
                                @click="setApiKey();" 
                                class="btn btn-primary m-2">Zmień klucz API</button
                            >    
                        </div>
                        
                    </div>                            
                </div>
                
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        this.logDev('Component mounted.');            
        window.addEventListener("keypress", e => {
    	   if(this.$refs.searchInput !== document.activeElement) {
    	       e.preventDefault();
    	       this.szukaj += String.fromCharCode(e.which);
    	       this.$refs.searchInput.focus();    	                                     
               this.fillTableData(0);               
    	   }
        });
    },

    
    created() {
        this.logDev('Component created.');
        this.getData();
      },

      
    data() {
        return {
    	    data: [],
            tableData: [],
            columns: ['id', 'name', 'login', 'created_at'],
            column_names: {'id' : 'ID', 'name' : 'nazwa', 'login' : 'login', 'created_at' : 'data dodania'},
            sort_column: '',
            sort_direction: '',
            page: 1,
            table_message: '',
            loading: false,
        };
    },

        
    props: {
        title: { type: String, required: true },
        dataurl: { type: String, required: true },
        pingurl: { type: String, required: true },
        rows: { type: Number, default: 10 },
    },
        

    methods: {
        logDev: function(msg) {
           if(window.location.href.indexOf("localhost") >= 0) {
    	       if(undefined!==msg)
                   console.log(msg);
           }
        },
            
        columnTitle(column_name) {
    	   return this.column_names[column_name].toUpperCase();
        },
            
        getData: function() {
    	    this.tableData = [];
    	    this.szukaj = '';
    	    this.sort_direction = [];
    	    this.sort_column = [];
    	    this.page = 1;
    	    this.num_pages = 0;
        	    
    	    this.logDev('Getting data...');
    	    this.loading = true;
        	    
            axios.post(this.dataurl, {api_key: this.getApiKey()})                
                .then(function(response){
            	
            	    this.logDev({'response': response});
            	    
                    if('ok'==response.data.msg) {
                        this.logDev('Response ok');
                        this.data = response.data.response;
                        if(this.fillTableData(0)) {
                    	    this.logDev('Table filled with data');
                        }
                    } else {
                        this.table_message = 'Błąd zwracania danych. Sprubój ponownie później';
                        this.logDev(`Response with: ${response.data.msg}`);                            
                    }
                                    	    
                }.bind(this))
                .catch(function (error) {
                    if (error.response) { //Response with no 2xx status
                        
                        if(401==error.response.status) {
                    	   this.table_message = 'Nieprawidłowy klucz API';
                        } else {
                    	   this.table_message = 'Błąd zwracania danych. Sprubój ponownie później';
                        }
                        
                        this.logDev({ 'status': error.response.status, 'headers': error.response.headers, 'data': error.response.data} );
                        
                    } else if (error.request) { //No response
                        
                        this.table_message = 'Brak odpowiedzi serwera';
                        this.logDev({'error': error.request});
                        
                    } else { // Something happened in setting up the request that triggered an Error                            
                        this.table_message = 'Błąd pobierania danych';
                        this.logDev({'error': error.message});
                    }
                    this.logDev(error.config);
                    
                }.bind(this))
                .finally(() => this.loading = false);                
            },
            
        sendID: function (id) {
    	   this.logDev(`Ping user_id: ${id}...`);
            axios.post(this.pingurl, {api_key: this.getApiKey(), user_id: id})                
            .then(function(response){
            
                this.logDev({'response': response});
                
                if('ok'==response.data.msg) {
                    this.logDev('Response ok');
                    alert('ID wysłano');
                } else {
                    this.table_message = 'Błąd zwracania danych. Sprubój ponownie później';
                    this.logDev(`Response with: ${response.data.msg}`);                            
                }                                        
            }.bind(this))            
            .catch(function (error) {
                if (error.response) { //Response with no 2xx status
                    
                    if(401==error.response.status) {
                       this.table_message = 'Nieprawidłowy klucz API';
                    } else {
                       this.table_message = 'Błąd zwracania danych. Sprubój ponownie później';
                    }
                    
                    this.logDev({ 'status': error.response.status, 'headers': error.response.headers, 'data': error.response.data} );
                    
                } else if (error.request) { //No response
                    
                    this.table_message = 'Brak odpowiedzi serwera';
                    this.logDev({'error': error.request});
                    
                } else { // Something happened in setting up the request that triggered an Error                            
                    this.table_message = 'Błąd pobierania danych';
                    this.logDev({'error': error.message});
                }
                
                this.logDev(error.config);
                
            }.bind(this));    	    
        },
            
        getApiKey: function() {
            
            if(undefined==localStorage.api_key || ''==localStorage.api_key || null==localStorage.api_key) {                 
                this.logDev('no API key');
                this.setApiKey(true);                   
            }
                 
            return localStorage.api_key;
         },
         
        setApiKey: function(disable_cancel) {
            let api_key = null;
            
            do {
                api_key  = prompt("Podaj klucz API"); 
            } while (''===api_key || (null===api_key && true==disable_cancel));
           
           if(null!==api_key) { 
               localStorage.api_key = api_key;
               this.logDev('API key stored');
               return true;
           }   
           
           return false;
        },            
        	    	
        	
    	filter: function(e) {
    	    this.logDev(`Filering for ${this.szukaj??''}`);
    	    
    	    this.fillTableData(0);
    	},
        	
        sortColumn: function(column_name) {
            if(0===this.tableData.length)
              return;
            
            if(this.sort_column==column_name) {
                switch (this.sort_direction) {
                    case 'up':
                        this.sort_direction = 'down';
                        break;
                    case '':
                        this.sort_direction = 'up';
                        break
                    default:
                        this.sort_direction = '';
                        this.sort_column = '';                             
                }
            } else {
                this.sort_direction = 'up';
                this.sort_column = column_name;
            }
            
            this.page = 1;
            
            this.logDev(`Sorting ${this.sort_column} ${(''==this.sort_direction ? 'none' : this.sort_direction)}`);             
            this.fillTableData(0);
        },
     
        fillTableData: function(start) {
            this.logDev(`Filling table data from ${start} to ${start+this.rows-1}`);
            
            var subarray = [...this.data];
            
            if(''!==this.szukaj) {
                subarray = subarray.filter( function(el, index, arr){
                  return (el.name.includes(this.szukaj) || el.login.includes(this.szukaj));
              }.bind(this));
              this.page = 1;  
              this.logDev(`Found ${subarray.length} records`);
            }
            
            if(''!=this.sort_column) {
                subarray = subarray.sort(function(a, b) {   
                    return (a[this.sort_column].toString().localeCompare(b[this.sort_column].toString(), undefined, { numeric: 'id'==this.sort_column} )) 
                    * ('up'==this.sort_direction ? 1 : (-1));                   
                }.bind(this));                  
            }
            
            this.tableData = subarray.slice(start, start + this.rows);
            
            if(this.tableData.length>0) {
                for (let i = this.tableData.length; i<this.rows; i++) {
                    this.tableData.push(this.columns.reduce((a, value) => {
                        return {...a, [value]: ''};
                    }, {}));
                }
            } else {
                this.table_message = 'Brak danych';
            }
            this.num_pages = Math.ceil(subarray.length / this.rows);
        },
        
        changePage: function(e) {
            let page = e.target.value; 
            this.logDev(`Going to page ${page}`);
            this.fillTableData(this.rows * (page-1));
        },        
    },
    

        	

    name: 'DataTable'  
}
    
</script>

<style scoped>
.datatable-title {
    text-align: center;
    font-size: 2rem;
}
.datatable-row {
    line-height: 2.6rem;
    min-height: 2.6rem;
    height: 2.6rem;
}

#change-api-key, #reset {
    float: right;
}
     
</style>