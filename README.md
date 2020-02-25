# highspot-demo-02242020
Elder Scrolls cards display -- dynamically retrieving results from an API.
Dependencies:
1. Vue.js 
2. Axios.js 
3. Bootstrap.css

Explanation: 
For ease of presentation, styles and scripts are included in one php page. In actual production, they would be separate files. (Also, since there is no PHP code on this page, it could just as easily be .html.) In the markup, vue components bind to methods that retrieve results from Elder Scrolls API using axios GET commands. 

"Infinite scroll" is achieved with a watcher that detects when the page is scrolled to the bottom and requests another page of results when that happens. Results are then pushed to array, causing components to dynamically update. Since "push" places results at end of array, this naturally creates the visual scrolling effect.

Search function works much the same way, but queries the API with an additional "name" parameter. If results exceed 20 (the given page limit) then they will scroll in similar fashion. A new search clears the results and starts with a fresh, empty array. I opted for a loose, rather than exact match for search results. My reasoning was that some of these cards have complicated names so a search that required exact wording might be of limited usefulness!

Bootstrap is used for the responsive grid.


