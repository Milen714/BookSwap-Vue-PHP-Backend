import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "../utils/axios.js";

export const useArticleStore = defineStore("article", () => {
  // State
  const articles = ref([]);
  const currentArticle = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Actions
  /**
   * Fetch all articles from the API
   */
  async function fetchArticles() {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get("/articles");
      articles.value = response.data;
      return response.data;
    } catch (err) {
      console.error("Error fetching articles:", err);
      error.value =
        err.response?.data?.message ||
        err.message ||
        "Failed to load articles. Please try again later.";
      articles.value = [];
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Fetch a single article by ID from the API
   * @param {number|string} articleId - Article ID
   */
  async function fetchArticleById(articleId) {
    const article = articles.value.find((a) => a.id == articleId);

    if (article) {
      console.log("found article in store:", article);
      currentArticle.value = article;
      return article;
    }

    loading.value = true;
    error.value = null;
    currentArticle.value = null;

    try {
      const response = await axios.get(`/articles/${articleId}`);
      currentArticle.value = response.data;
      console.log("Article fetched:", currentArticle.value);
      return response.data;
    } catch (err) {
      console.error("Error fetching article:", err);
      if (err.response?.status === 404) {
        error.value = "Article not found";
      } else {
        error.value =
          err.response?.data?.message ||
          err.message ||
          "Failed to load article. Please try again later.";
      }
      currentArticle.value = null;
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Update an article
   * @param {Object} article - Article object with id and data to update
   */
  async function updateArticle(article) {
    const articleId = article.id || article._id;
    loading.value = true;
    error.value = null;

    try {
      await axios.put(`/articles/${articleId}`, article);
      await fetchArticles();
    } catch (err) {
      console.error("Error updating article:", err);
      error.value =
        err.response?.data?.message ||
        err.message ||
        "Failed to update article. Please try again.";
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Create a new article
   * @param {Object} article - Article object with data to create
   */
  async function createArticle(article) {
    loading.value = true;
    error.value = null;

    try {
      await axios.post("/articles", article);
      await fetchArticles();
    } catch (err) {
      console.error("Error creating article:", err);
      error.value =
        err.response?.data?.message ||
        err.message ||
        "Failed to create article. Please try again.";
    } finally {
      loading.value = false;
    }
  }

  return {
    // State
    articles,
    currentArticle,
    loading,
    error,
    // Actions
    fetchArticles,
    fetchArticleById,
    updateArticle,
    createArticle,
  };
});
